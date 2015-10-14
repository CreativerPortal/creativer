angular.module('angularSearchTree', [])
    .factory('searchTree', [function() {


        function SearchTree(){

            var main_obj;

            function tree(keyObj,obj) {
                var p, key, val, tRet;
                if(main_obj == undefined){
                    main_obj = obj;
                }
                for (p in keyObj) {
                    if (keyObj.hasOwnProperty(p)) {
                        key = p;
                        val = keyObj[p];
                    }
                }

                for (p in obj) {
                    if (p == key) {
                        if (obj[p] == val) {
                            obj.selected = true;
                            return main_obj;
                        }
                    } else if (obj[p] instanceof Object) {
                        if (obj.hasOwnProperty(p)) {
                            tRet = tree(keyObj,obj[p]);
                            if (tRet) { return tRet; }
                        }
                    }
                }
                return false;
            };

            return tree;
       };


       return SearchTree;

    }
    ]);
