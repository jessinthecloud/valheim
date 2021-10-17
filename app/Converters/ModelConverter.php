<?php

namespace App\Converters;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\Tools\CraftingStation;
use App\Models\Tools\PieceTable;
use App\Models\Tools\RepairStation;
use App\Models\Craftables\Item;

class ModelConverter implements Converter
{
    /**
     * Parse fields, create model and attach related models
     *
     * @param array                      $data
     * @param string                     $class
     * @param \App\Converters\DataParser $parser
     *
     * @return mixed
     */
    public function convert( array $data, string $class, DataParser $parser )
    {
        // create/convert names
        $entity = $parser->parse( $data, $class );
        // make sure slug is set and unique
        $entity['slug'] = $entity['item_slug'] = $entity['piece_slug'] = isset( $entity['slug'] ) ?
            $parser->checkAndSetSlug( $entity['slug'], $class ) :
            null;

        if ( empty( $entity['slug'] ) ) {
            // if no slug, don't bother
            return null;
        }

        // only try to insert columns that exist
        $table = $parser->parseTable( $class );
        $db_column_values = Arr::only( $entity, Schema::getColumnListing( $table ) );

        // requirements also need to check amount and per level 
        // because slug is not unique to Requirement
        $unique_fields = ( Str::contains( $class, ["Requirement"] ) ) ? $db_column_values : ['slug' => $entity['slug']];

        // create model
        // check if already exists
        // find existing or create model from values
        $model = $class::firstOrCreate(
        // array of unique key value to check 
            $unique_fields,
            // array of values to use
            $db_column_values
        );

        if ( defined( $class . '::RELATION_INDICES' ) ) {
            // get any that are also relationships that need to be mapped
            // use intersect to compare by keys and avoid issue with
            // PHP trying to compare multidimensional values
            $relations = array_intersect_key( $entity, $class::RELATION_INDICES );

            // convert relations
            $relations = collect( $relations )->map(
                function ( $relation, $key ) use ( $model, $parser, $entity, $class, $relations ) {
                    // $key is the unique array index / DB column            
                    $relation_class = $class::RELATION_INDICES[ $key ]['class'];
                    $relation_method = $class::RELATION_INDICES[ $key ]['method'];
                    // determine relation attach function attach() vs associate()
                    $attach_function = $class::RELATION_INDICES[ $key ]['relation'];

                    // need to send array to the convert function
                    if ( !is_array( $relation ) ) {
                        // convert & attach relation to model
                        return $this->convertAndAttachRelation(
                            $model,
                            $relations,
                            $relation_class,
                            $relation_method,
                            $attach_function,
                            $parser,
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
                        function ( $entity ) use (
                            $relation,
                            $relation_class,
                            $relation_method,
                            $parser,
                            $model,
                            $attach_function
                        ) {
                            // convert & attach relation to model
                            return $this->convertAndAttachRelation(
                                $model,
                                $entity,
                                $relation_class,
                                $relation_method,
                                $attach_function,
                                $parser,
                                $entity
                            );
                        }
                    );

                    $flat_relation_data = $this->convertAndAttachRelation(
                        $model,
                        $relation,
                        $relation_class,
                        $relation_method,
                        $attach_function,
                        $parser,
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
            $related = Item::firstWhere( 'slug', $entity['slug'] );

            if ( isset( $related ) ) {
                $this->attachRelated( $model, $related, $relation_method, $attach_function );
            }

            return $related;
        }

        // recipes should not convert their relation (item), only find existing and attach
        if ( $relation_method === 'creation'/* || isset($relations['piece_slug'])*/ ) {
            // find existing item b/c only item_slug/piece_slug exists
            // when trying to find related
            $related = ( isset( $relations['item_slug'] )
                    ? $relation_class::where( 'slug', $relations['item_slug'] )->first()
                    : null )
                ?? ( isset( $relations['piece_slug'] )
                    ? $relation_class::where( 'slug', $relations['piece_slug'] )->first()
                    // sometimes recipes don't have item slug that matches 
                    : null )
                ?? ( isset( $relations['item_slug'] )
                    ? $relation_class::where(
                        'name',
                        Str::replace( '-', ' ', $relations['item_slug'] )
                    )->first()
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
        // relation data
            $relation_data,
            // relation's class
            $relation_class,
            // parser object
            $parser
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

        if ( is_array( $model->$relation_method() ) ) {
            // some models have multiple methods for a related model class (SharedData -> StatusEffect)

            collect( $model->$relation_method() )->each(
                function ( $method ) use ( $model, $relation, $attach_function ) {
                    // don't use ALL methods to attach,
                    // just the one with matching type (e.g., status effect)
                    if ( isset( $relation['type'] ) && Str::startsWith( $method, $relation['type'] ) ) {
                        $model->$method()->$attach_function( $relation );
                    }
                }
            );

            // attach saves by default
            if ( $attach_function !== 'attach' ) {
                $model->save();
            }

            return;
        }

        $model->$relation_method()->$attach_function( $relation );
        // attach saves by default
        if ( $attach_function !== 'attach' ) {
            $model->save();
        }
    }
}