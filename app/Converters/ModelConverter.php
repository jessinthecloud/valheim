<?php

namespace App\Converters;

use App\Models\Items\Craftables\Items\CraftableItem;
use App\Models\Items\Item;
use App\Models\Items\StatusEffect;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\Tools\CraftingStation;
use App\Models\Tools\PieceTable;
use App\Models\Tools\RepairStation;
use App\Models\Recipes\Requirement;

class ModelConverter implements Converter
{
    protected DataParser $parser;
    
    /**
     * Parse fields, create model and attach related models
     *
     * @param array                      $data
     * @param string                     $class
     * @param \App\Converters\DataParser $parser
     *
     * @return mixed
     * @throws \Exception
     */
    public function convert( array $data, string $class, DataParser $parser )
    {
        $this->parser = $parser;

        // create/convert names
        $entity = $this->parser->parse( $data, $class );
        // make sure slug is set and unique
        $entity['slug'] = $entity['item_slug'] = $entity['piece_slug'] = isset( $entity['slug'] ) ?
            $this->parser->checkAndSetSlug( $entity['slug'], $class ) :
            null;

        if ( empty( $entity['slug'] ) ) {
            // if no slug, don't bother
            return null;
        }

        // only try to insert columns that exist
        $table = $this->parser->parseTable( $class );
        $db_column_values = Arr::only( $entity, Schema::getColumnListing( $table ) );

        // requirements are always unique
        if( Str::contains( $class, ["Requirement"] ) ){
//dump($class, $db_column_values, $entity);
            // create model from values
            $model = new $class(
            // array of values to use
                $db_column_values
            );
            $model->save();
        }
        else{
            // create model
            // check if already exists
            // find existing or create model from values
            $model = $class::firstOrCreate(
            // array of unique key value to check 
                ['slug' => $entity['slug']],
                // array of values to use
                $db_column_values
            );
        }

        if ( defined( $class . '::RELATION_INDICES' ) || isset($class::$relation_indices) ) {
            // constant can't be part of trait (CraftableItem)    
            $relation_indices = $class::$relation_indices ?? $class::RELATION_INDICES;

            // get any that are also relationships that need to be mapped
            // use intersect to compare by keys and avoid issue with
            // PHP trying to compare multidimensional values
            $relations = array_intersect_key( $entity, $relation_indices );

            // convert relations
            $relations = collect( $relations )->map(
                function ( $relation, $key ) use ( $relation_indices, $model, $entity, $class, $relations ) {
                    // $key is the unique array index / DB column            
                    $relation_class = $relation_indices[ $key ]['class'];
                    $relation_method = $relation_indices[ $key ]['method'];
                    // determine relation attach function attach() vs associate()
                    $attach_function = $relation_indices[ $key ]['relation'];

                    // need to send array to the convert function
                    if ( !is_array( $relation ) ) {
                        // convert & attach relation to model
                        return $this->convertAndAttachRelation(
                            $model,
                            $relations,
                            $relation_class,
                            $relation_method,
                            $attach_function,
                            $this->parser,
                            $entity
                        );
                    }

                    // make sure $data is more than 1 dimensional before looping
                    // otherwise, make $data an array and convert its names directly 
                    // check all, not just first
                    $flat_relation_data = collect( $relation )->filter( function ( $entity ) {
                        return !is_array( $entity );
                    } );

                    $multi_relation_data = collect( $relation )->diffAssoc( $flat_relation_data );

                    $multi_relation_data = $multi_relation_data->map(
                        function ( $data ) use (
                            $relation,
                            $relation_class,
                            $relation_method,
                            $model,
                            $attach_function,
                            $entity
                        ) {
                            // convert & attach relation to model
                            $related = $this->convertAndAttachRelation(
                                $model,
                                $data,
                                $relation_class,
                                $relation_method,
                                $attach_function,
                                $this->parser,
                                $entity
                            );
                            
                            return $related;
                        }
                    );

                    $flat_relation_data = $this->convertAndAttachRelation(
                        $model,
                        $relation,
                        $relation_class,
                        $relation_method,
                        $attach_function,
                        $this->parser,
                        $entity
                    );

                    return $multi_relation_data->merge( $flat_relation_data )->all();
                }
            );
        }

        return $model;
    } // end convert()

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param array                               $relation_data
     * @param string                              $relation_class   class to attach
     * @param string                              $relation_method  model relationship method
     * @param string                              $attach_function  function that attaches
     *                                                              this kind of relationship to model
     * @param \App\Converters\DataParser          $parser
     * @param array                               $entity
     *
     * @return mixed
     */
    protected function convertAndAttachRelation(
        Model $model,
        array $relation_data,
        string $relation_class,
        string $relation_method,
        string $attach_function,
        DataParser $parser,
        array $entity
    ) {
        // requirements should not convert their relation (item), only find existing and attach
        if ( $relation_method === 'item' ) {
//dump($entity);        
            $related = Item::firstWhere( 'var_name', $entity['var_name'] );
//dump($related);
            if ( isset( $related ) ) {
                $this->attachRelated( $model, $related, $relation_method, $attach_function );
            }
            return $related;
        }

        // sharedData should not convert their status effects, only find existing and attach
        // requirements should not convert their relation (item), only find existing and attach
        if ( $relation_method === 'statusEffects' ) {

            // remove empty
            $effects = (isset($entity['status_effects']) ? (isset($entity['status_effects']['slug']) ? collect($entity['status_effects'])->filter()->all() : null) : null);

            if ( empty($effects) ) {
                // no status effects
                return null;
            }

            // make sure there is something to attach
            return collect( $model->$relation_method() )->map(
                function ( $method ) use ( $effects, $model, $entity, $attach_function ) {

                    return collect($effects)->map(function($effect) use($entity, $model, $method, $attach_function) {

                        // get slug for effect
                        $effect = $this->parser->convertNames($effect, StatusEffect::class);
                        $related = StatusEffect::firstWhere( 'slug', $effect['slug'] );

                        // don't use ALL methods to attach,
                        // just the one with matching type (e.g., status effect)
                        if ( isset( $related ) && isset( $effect['type'] ) && Str::startsWith( $method, $effect['type'] ) ) {
                            $model->$method()->$attach_function( $related );
                            $model->save();
                        }

                        return $related;
                    });
                }
            )->flatten()->unique('id')->first();
        } // endif status effects

        // recipes should not convert their relation (item), only find existing and attach
        if ( $relation_method === 'creation'/* || isset($relations['piece_slug'])*/ ) {

            // find existing item b/c only item_slug/piece_slug exists
            // when trying to find related
            $related = ( isset( $relation_data['item_slug'] )
                    ? $relation_class::where( 'slug', $relation_data['item_slug'] )->withoutGlobalScope('enabled')->first()
                    : null )
                ?? ( isset( $relation_data['piece_slug'] )
                    ? ($relation_class::where( 'slug', $relation_data['piece_slug'] )->withoutGlobalScope('enabled')->first())
                    // sometimes recipes don't have item slug that matches 
                    : null )
                ?? ( isset( $relation_data['item_slug'] )
                    ? $relation_class::where(
                        'name',
                        Str::replace( '-', ' ', $relation_data['item_slug'] )
                    )->withoutGlobalScope('enabled')->first()
                    : null )
                ?? ( ( null !== $model->name )
                    ? $relation_class::where(
                        'name',
                        $model->name
                    )->first()
                    : null );

            // attach relation to model
            isset( $related ) ?
                $this->attachRelated( $model, $related, $relation_method, $attach_function ) :
                $related = null;

            return $related;
        } // end creation() or piece_slug

        // piece recipe should not convert crafting station or piece table, only find existing and attach
        if ( $relation_method === 'craftingStation'
            || ( $relation_method === 'craftingDevice'
                && Str::contains( $relation_class, 'CraftingStation' ) )
            || ( $relation_method === 'repairStation'
                && Str::contains( $relation_class, 'RepairStation' ) )
            || ( $relation_method === 'craftingDevice'
                && Str::contains( $relation_class, 'PieceTable' ) )
        ) {
            $related = ( isset( $entity['raw_crafting_station_name'] ) && Str::contains(
                    $relation_class,
                    'CraftingStation'
                ) ? CraftingStation::firstWhere( 'raw_name', $entity['raw_crafting_station_name'] ) : null )
                ?? ( isset( $entity['raw_repair_station_name'] ) && Str::contains(
                    $relation_class,
                    'RepairStation'
                ) ? RepairStation::firstWhere( 'raw_name', $entity['raw_repair_station_name'] ) : null )
                ?? ( isset( $entity['piece_table_true_name'] ) && Str::contains(
                    $relation_class,
                    'PieceTable'
                ) ? PieceTable::firstWhere( 'true_name', $entity['piece_table_true_name'] ) : null )
                ?? null;

            if ( isset( $related ) ) {
                $this->attachRelated( $model, $related, $relation_method, $attach_function );
            }
            return $related;
        }

        $related = $this->convert(
            $relation_data,
            $relation_class,
            $this->parser
        );

        // if null, quit
        if ( empty( $related ) ) {
            return null;
        }

        // attach relation to model
        $this->attachRelated( $model, $related, $relation_method, $attach_function );

        return $related;
    }

    /**
     * Find and save relation to model
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param \Illuminate\Database\Eloquent\Model $relation
     * @param string                              $relation_method
     * @param string                              $attach_function
     *
     * @return void
     */
    protected function attachRelated( Model $model, Model $relation, string $relation_method, string $attach_function )
    {
        if ( null === $model->$relation_method() ) {
            // no relation methods
            return;
        }

        $model->$relation_method()->$attach_function( $relation );

        // attach saves by default
        if ( $attach_function !== 'attach' ) {
            $model->save();
        }
    }
}