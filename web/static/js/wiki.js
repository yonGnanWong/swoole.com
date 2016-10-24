function wikiCreateTree(data){
    var root = [];
    for(var i = 0;i<data.length; i++) {
        if(i["pid"]==0) {
            root.append(data[i]);
        }
    }
    alert("xx");
}