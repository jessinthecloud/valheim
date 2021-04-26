using System;
using System.Collections.Generic;
using System.Text.RegularExpressions;
using System.Linq;
using System.Text;
using System.Reflection;
using System.IO;
using JotunnLib;
using JotunnDoc;
using JotunnDoc.Docs;
using JotunnLib.Managers;
using UnityEngine;
using BepInEx;


namespace OutputApp
{
    static class Program
    {
        static void Main(string[] args)
        {
            // System.Diagnostics.Debug.WriteLine()
            //JotunnLib.Main lib = new JotunnLib.Main(); // configpath/null arg error
            //System.Diagnostics.Debug.WriteLine("\nJOTUNNLIB MAIN!\n");
            
            ItemManager manager = new ItemManager();
            manager.Init();

            

            //JotunnDoc.Docs.RecipeDoc doc = new JotunnDoc.Docs.RecipeDoc();
            //JotunnDoc.JotunnDoc doc = new JotunnDoc.JotunnDoc();

        }
    }
}
