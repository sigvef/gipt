<!doctype html>
<html>
<head>
<title>Gabong Interaktiv Poengtavle :: Arktis by Sigve Sebastian Farstad</title>

<script language="javascript" src="http://www.google.com/jsapi"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>

<? require('../stats.inc');?>

<script>

numofcolors = 15;
colormap = new Array();
colormap[0] = "E54661";
colormap[1] = "FFA644";
colormap[2] = "998A2F";
colormap[3] = "2C594F";
colormap[4] = "002D40";
colormap[5] = "FF00FF";
colormap[6] = "00FFFF";
colormap[7] = "AA2200";
colormap[8] = "00AA22";
colormap[9] = "AA0022";
colormap[10] = "2200AA";
colormap[11] = "AA5530";
colormap[12] = "AA33AA";
colormap[13] = "DD7300";
colormap[14] = "0F40C2";



 var queryString = '';
      var dataUrl = '';

      function drawChart() {
        $('#chart').html("");
        if (dataUrl.length > 0) {
          var query = new google.visualization.Query(dataUrl);
          query.setQuery(queryString);
          query.send(handleQueryResponse);
        } else {
          var dataTable = new google.visualization.DataTable();
          dataTable.addRows(10);
          
          for(var i=0;i<GIPT.players.length;i++){
            dataTable.addColumn('number');
          }
          for(var i=0;i<GIPT.players.length;i++){
            j= 0;
            score = 0;
            GIPT.players[i].jq.children('.scores').children('.score').each(function(){
                if(!isNaN(parseInt(this.value))){score += parseInt(this.value);
                if(parseInt(score)/100 == Math.floor(parseInt(score)/100) && parseInt(this.value)!= 0) score = Math.ceil(score /2);
                }
                dataTable.setValue(j,i,score);
                j++;
            });
          }
          

          draw(dataTable);
        }
      }

      function draw(dataTable) {
        var vis = new google.visualization.ImageChart(document.getElementById('chart'));
        var lestring = "";
        $('.name:not(:last-child)').each(function(){
            lestring = lestring+'|'+ this.value;
            console.log(this);
        });
        console.log(lestring);
        var colorstring = "";
        for(var i=0;i<GIPT.players.length;i++){
            colorstring = colorstring+','+colormap[i%numofcolors];
        }
        
        
        var options = {
             chs: '600x450',
              cht: 'lc',
              chf: 'bg,s,222222',
              chco: colorstring.substring(1),
              chd: 's:Xhiugtqi,UbdacPTY,YfiglihbaWVUNNLORVVR',
              chdl: lestring.substring(1,lestring.length-1),
              chg: '14.3,-1,1,1',
              chls: '2,4,0|1|1',
        };
        vis.draw(dataTable, options);
      }

      function handleQueryResponse(response) {
        if (response.isError()) {
          alert('Error in query: ' + response.getMessage() + ' ' + response.getDetailedMessage());
          return;
        }
        draw(response.getDataTable());
      }

      google.load("visualization", "1", {packages:["imagechart"]});

</script>

<style>

html,body{
    background: #222;
    
}


    input{
        background:#fff;
    }

    .player{
        float:left;
        padding: 10px;
        margin: 10px;
        border: 1px solid #000;
    }
    
    .name{
        padding:10px;
        border:1px solid #000;
        font-size:1.2em;
    }
    
    .name.classy{
        font-family:Garamond,Georgia,serif;
        background:transparent;
        border: 0px solid #000;
    }
    
    .player:last-child{
        background:rgba(255,255,255,0.5) !important;
    }
    
    .player:last-child .name.classy{
        padding:10px;
        border:1px solid #000;
        font-size:1.2em;
        font-family: sans-serif;
        background:#fff;
    }
    
    .score{
        display:block;
        padding:10px;
        border: 1px solid #222;
    }
    
    .score:last-child{
        background:rgba(255,255,255,0.5) !important;
    }
    
    #chart{
        clear:both;
    }
</style>

<script>

$('.score').live('keyup paste', function(){
    for(var i=0;i<GIPT.players.length-1;+i++){
        GIPT.players[i].computeScore();
    }
});

$('.score:last-child').live('keyup paste', function() {
    if(event.keyCode != 9){
        GIPT.players[$($(this).parent()[0]).parent()[0].id].addScore();
    }
});

$('.name').live('focus', function() {
    $(this).removeClass('classy');
});
$('.name').live('blur', function() {
    $(this).addClass('classy');
});

$('.name').live('keyup paste', function() {
    if(event.keyCode != 9){
        if($(this).parent()[0].id == GIPT.players.length-1){
            GIPT.addPlayer("New Player...");
            GIPT.players[$(this).parent()[0].id].addScore();
        }
    }
});

function Player(_name,_jq,_id){
    this.points = new Array();
    this.name = _name;
    this.id = _id;
    this.jq = _jq;
    this.addScore = function(){
        if(this.jq.children('.scores').children('.score').size() < 10){
            this.jq.children('.scores').append('<input type="text" class="score" placeholder="0" tabindex='+this.jq.children().size()*100+this.id+'></input>');
            GIPT.players[this.id].computeScore();
        }
        GIPT.repairTabIndex();
    }
    this.computeScore = function(){
        score = 0;
        this.jq.children('.scores').children('.score').each(function(){
            if(!isNaN(parseInt(this.value))){
                score+= parseInt(this.value);
                if(parseInt(score)/100 == Math.floor(parseInt(score)/100)) score = Math.ceil(score /2);
            }
            if(this.value.toUpperCase() == "GABONG"){
                this.value = "θ";
            }
        });
        this.jq.children('.totalScore').html(score);
        drawChart();
    }
}
function GabongInteraktivPoengTavle(){
    this.players = new Array();
    this.jq = $('.GIPT');
    
    this.addPlayer = function(_name){
        this.players.push(new Player(name,this.jq.append('<div class="player" id="'+(this.players.length)+'"><input type="text" class="name" placeholder="'+_name+'"></input><div class="scores"></div><div class="totalScore"></div></div>').children('#'+this.players.length)));
        this.players[this.players.length-1].id=this.players.length-1;
        this.players[this.players.length-1].jq.css({'background':"#"+colormap[(this.players.length-1)%numofcolors]+""});
        this.repairTabIndex();
return this.players.length-1;
    }
    this.removePlayer = function(id){
        this.players[id] = null;
    }
    
    this.repairTabIndex = function(){
        tabindex = 1;
        for(var i=0;i<this.players.length;i++){
            this.players[i].jq.children('.name').attr('tabindex',tabindex);
            tabindex++;
        }
        for(var j=0;j<10;j++){
        for(var i=0;i<this.players.length;i++){
            if($(this.players[i].jq.children('.scores').children('.score').eq(j)) != null){
            $(this.players[i].jq.children('.scores').children('.score').eq(j).attr('tabindex',tabindex));
            tabindex++;
            }
        }
        }
    }
}


$(function(){
    GIPT = new GabongInteraktivPoengTavle();
    GIPT.addPlayer("New Player...");
});

</script>


</head>
<body>

<div class="GIPT">



</div>
<div id="chart"></div>
</body>
</html>
