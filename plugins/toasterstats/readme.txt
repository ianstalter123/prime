Toasterstats plugin shows table with site statistics about products(number of products, number of brands), users(number of users, admins etc), pages(pages, categories count, number of draft pages), templates, installed plugins.
    1. First insert in index.html for dashboard plugin menu name of your html files(pages for dashboard)
example: {dashboardmenu}
				index				
				quotes
				sales
				clients
			{/dashboardmenu}

2. Returns quantity of sales Today {$plugin:toasterstats:sales:today}
3. Returns quotes Requests Today {$plugin:toasterstats:quotes:today}
4. Returns earned Today{$plugin:toasterstats:money:today}
5. Returns orders Today {$plugin:toasterstats:orders:today}
6. Return today's top sellers example: {$plugin:toasterstats:products:topsellers:today:10}
7. Return latest customers example: {$plugin:toasterstats:customer:new:10}
8. Return all site statistic {$plugin:toasterstats:Sitestatistic}
9. Return control options for statistic {$plugin:toasterstats:control}
10. Return top sellers for period {$plugin:toasterstats:products:topsellers:days:7:10}.
11. Label {$plugin:toasterstats:label:days:7}.
Grafhs section:
    On grafhs you can show sales or quotes or quotes and sales together
    Also you can change period of showing statistic data. You can use 'days', 'week', 'month', 'year', 'totalPeriod'.
    Type of graphs: linechart, piechart, geo.
    Type of returns data count, amount.
    The last two parameters it is width and height of graph.
Examples:
1. Sales and quotes count graph {$plugin:toasterstats:graph:columnchart:count:quotes|sales:days:7:400:400}
2. {$plugin:toasterstats:money:days:7}
3. {$plugin:toasterstats:orders:days:7}
4. {$plugin:toasterstats:graph:columnchart:amount:sales|quotes:days:7:400:400}
5. {$plugin:toasterstats:Graph:linechart:count:sales|quotes:days:20:450:450}
6. {$plugin:toasterstats:Graph:piechart:amount:type:days:20:400:450}
7. {$plugin:toasterstats:Graph:linechart:averageamount:sales|quotes:days:20:450:450}
8. {$plugin:toasterstats:Graph:piechart:amount:customer:days:20:400:450}
9. {$plugin:toasterstats:Graph:piechart:amount:product:days:20:400:450}
10. {$plugin:toasterstats:Graph:piechart:amount:brand:days:20:400:450}
11. {$plugin:toasterstats:Graph:columnchart:count:tag:days:20:400:400}
12. {$plugin:toasterstats:Graph:geo:map:days:20:400:400}