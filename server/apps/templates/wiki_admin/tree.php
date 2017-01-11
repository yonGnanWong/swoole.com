<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <!-- Custom styles for this template -->
    <link href="/static/css/theme.css" rel="stylesheet">
    <link rel="StyleSheet" href="/static/js/tree/dtree.css" type="text/css" />
    <script src="/static/js/jquery.js"></script>
    <script src="/static/js/dtree.js"></script>
    <title>Swoole文档中心</title>
    <base target="main" />
</head>
<body>
    <div class="main_left">
        <div class="inner-containner">
            <div id="tree">
            </div>
        </div>
    </div>
    <script type="text/javascript">
        var wiki_tree_data = <?=$tree?>;
        var wiki_id_map = [];
        $(document).ready(function() {
            d = new dTree('d');
            d.config.useLines = false;
            d.config.useCookies = false;
            d.onOpen = function (i, open) {
                var nodeId = wiki_id_map[i];
                if (!open) {
                    window.localStorage.removeItem('wiki_node_open_' + nodeId);
                } else {
                    window.localStorage.setItem('wiki_node_open_' + nodeId, true);
                }
            };

            var link = '';
            var open;
            var node_id;
            for (var i = 0; i < wiki_tree_data.length; i++) {
                if(wiki_tree_data[i]['link'].length > 0 && (wiki_tree_data[i]['link'].substring(0, 7)=='http://'
                        || wiki_tree_data[i]['link'].substring(0, 8)=='https://')) {
                    link = wiki_tree_data[i]['link']
                } else {
                    link = '/wiki_admin/main/'+wiki_tree_data[i]['id'];
                }
                node_id = wiki_tree_data[i]["id"];
                wiki_id_map[i] = node_id;
                open = window.localStorage.getItem('wiki_node_open_' + node_id);
                if (open === null) open = false;
                d.add(wiki_tree_data[i]["id"],
                        wiki_tree_data[i]["pid"],
                        wiki_tree_data[i]["text"],
                        link,
                        wiki_tree_data[i]["text"],
                        null, null, null, open);
            }
            $('div#tree').html(d.toString());
        });
    </script>
</body>
</html>
