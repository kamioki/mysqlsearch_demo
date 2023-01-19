<!DOCTYPE html>
<html lang="ja">
<head>
<title>TITLE</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<script src="https://maps.googleapis.com/maps/api/js?key=API"></script>
<script src="https://unpkg.com/@googlemaps/markerclustererplus/dist/index.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src ="https://cdn.jsdelivr.net/gh/DeuxHuitHuit/quicksearch/dist/jquery.quicksearch.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/list.js/2.3.1/list.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.bundle.min.js"></script>
<link rel="stylesheet" type="text/css" href="style.css?2278798471" />
<script>
jQuery(function() {
    $(window).scroll(function(){
        var now = jQuery(window).scrollTop();
    if(now > 900){
      $('.pagetop').fadeIn('slow');
    }
    else{
      $('.pagetop').fadeOut('slow');
    }
    });
    $('.gototop').click(function(){
    $('body,html').animate({scrollTop: 0}, 500);
  });
  $(window).load(function() {
        $(".loading").fadeOut("slow");
        });
    $('input#refine').quicksearch('table#tablelist tbody tr', {
        'noResults': 'tr#noresults',
        'loader':'div.loading',
        'delay': 1000
    });
});
</script>
</head>

<body>
<div class="chart"></div>
<h1>TITLE</h1>
	<form action="#" method="GET" class="search">
        <input type="text" minlength="2" name="sp" placeholder="ENTER" value="<?php if( !empty($_GET['sp']) ){ echo $_GET['sp']; } ?>">
        <button type="submit" name="like">部分一致</button>
        <button type="submit" name="exact">完全一致</button>
<?php while(true) {
//DBinfo
$server='SERVERINFO';
$user="USERNAME";
$pw="PASSWORD";
    try
    {
        $con=new PDO("mysql:host=$server;",$user,$pw);
        $con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $e)
    {
        echo '<br>'.$e->getMessage();
    }

    $msg = '';
    $birdlist='';

    if(!empty($_GET['sp']))
    {
        if(isset($_GET['like'])){
            $userinput = $_GET['sp'];
            $stmt = $con->prepare("SELECT * FROM TABLENAME WHERE SP LIKE '%$userinput%'");
            }
        if(isset($_GET['exact'])){
            $userinput = $_GET['sp'];
            $stmt = $con->prepare("SELECT * FROM TABLENAME WHERE SP = '$userinput'");
        }

        $stmt->execute();
        $count = $stmt -> rowCount();

        if($count == 0) {
            $msg = '該当データがありません';
        }
        if($count > 10000) {
            $msg = '検索結果が1万件以上あるため、簡易リストを表示します';
        }

        $birdlist = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //filter empty latlng
        $locations = array_filter($birdlist, function ($row) {
            return ($row['latlng']);
        });
        //select latlng column, overwrite same latlng
        $latlnglist = array_column($locations, 'latlng');
        //reduce data
        //array_splice($locations, 200);
    }
    else{
         $msg = '入力欄に検索語を入力（2文字以上）';
        }
?>

        </form>
        <p class="msg"><?php echo $msg;?></p>

<?php if($count > 10000){?>
<div class="loading">データ読み込み中...</div>
<h2><a href="https://www.inaturalist.org/search?q=<?php echo $userinput ?>" target="_blank"><?php echo $userinput ?></a>を含む<span id="hits"></span>件の検索結果</h2>
<div id="tablelist">
<div class="quicksearch"><label for="search">絞り込み：</label><input class="search" type="text" placeholder="スペースでAND検索"></div>
<ul class="pagination"></ul>
<ol class="list">
<?php foreach($birdlist as $row) {?>
    <li><p class="sp"><?php echo $row['year'].'.'.$row['month'].'.'.$row['date'].' '.$row['sp'].' '; if(!empty($row['subsp'])){echo '('.$row['subsp'].') ';}; echo $row['num'].' '.$row['city'].' '.$row['place'].' '.$row['notes'].' '.$row['recorder']; if(!empty($row['observer'])){echo ' - '.$row['observer'];};?></p></li>
<?php }?>
</ol>
</div>
<?php }?>

<?php if($count > 0 && $count < 10000){?>
<script>
const jsonlist = <?php echo json_encode($birdlist, JSON_UNESCAPED_UNICODE) ?>;
</script>

<div class="loading">データ読み込み中...</div>
<h2><a href="https://www.inaturalist.org/search?q=<?php echo $userinput ?>" target="_blank"><?php echo $userinput ?></a>を含む<?php echo $count ?>件の検索結果</h2>
<div class="quicksearch"><label for="refine">絞り込み：</label><input id="refine" type="text" placeholder="半角スペースでAND検索"></div>
<p>白は記録のない月/市町。テーブルの項目名をクリックでソート可。</p>


<div class="graph"><canvas id="monthchart"></canvas></div>
<div class="graph"><canvas id="citychart"></canvas></div>

<?php if (!empty($locations)) {?>
<script>
var locations = <?php echo json_encode($latlnglist) ?>;
var content = [<?php foreach($locations as $row){?>"<?php echo $row['sp'];?> <a href='#<?php echo $row['id'];?>'><?php echo $row['year'].'.'.$row['month'].'.'.$row['date'];?></a>",<?php }?>];
</script>
<div id="map"></div>
<?php } ?>

<table id="tablelist">
    <thead>
        <tr>
            <th class="sort id" data-sort="id">ID</th>
	        <th class="sort sp" data-sort="sp">種名</th>
            <th class="sort nm" data-sort="nm">個体数</th>
            <th class="sort dt" data-sort="dt">年月日</th>
            <th class="sort lc" data-sort="lc">地名</th>
            <th class="sort nt" data-sort="nt">観察内容</th>
            <th class="sort rc" data-sort="rc">記録者 - 観察者</th>
	      </tr>
        </thead>
        <tbody class="list">
<?php foreach($birdlist as $row) {?>
<tr id="<?php echo $row['id'];?>"><td class="id"><?php echo $row['id'];?></td><td class="sp"><?php echo $row['sp'];if(!empty($row['subsp'])){echo ' ('.$row['subsp'].')';};?></td><td class="nm"><?php echo $row['num'];?></td><td class="dt"><?php echo $row['year'].'.'.$row['month'].'.'.$row['date'];?></td><td class="lc"><?php echo $row['city'].' '.$row['place'];?></td><td class="nt"><?php echo $row['notes'];?></td><td class="rc"><?php echo $row['recorder'];if(!empty($row['observer'])){echo ' - '.$row['observer'];};?></td></tr>
<?php }?>
    <tr id="noresults">
        <td colspan="7">絞り込み条件に一致するデータがありません</td>
    </tr>
    </tbody>
</table>

<script src="chart.js?6332999"></script>
<script src="googlemap.js?6932999"></script>
<?php }?>

<p class="pagetop" style="display: block;"><a class="gototop" href="#pagetop">▲TOP</a></p>

<?php break; } ?>

<script>
    var options = {
        valueNames: [ 'id','sp','nm','dt','lc','nt','rc'],
        page:10000,
        pagination:true,
        searchDelay:1000,
        };
    var userList = new List('tablelist', options);
    userList.on('searchComplete', function(a){
        $("#hits").html(a.matchingItems.length);
    });
    $("#hits").html(userList.size);
</script>
</body>
</html>