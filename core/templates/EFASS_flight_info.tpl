<h3>Flight detail report</h3>

<?php>
/*
	Added in 2.0!
*/
$chart_width = '800';
$chart_height = '250';


/* Don't need to change anything below this here ///efass/altgraph/7.$pirep->pirepid*/
?>
<div align="center" style="width: 100%;">
<div align="center" id="altitude_chart"></div>
</div>

<script type="text/javascript" src="<?php echo fileurl('/lib/js/ofc/js/swfobject.js')?>"></script>
<script type="text/javascript">
swfobject.embedSWF("<?php echo fileurl('/lib/js/ofc/open-flash-chart.swf');?>", 
	"altitude_chart", "<?php echo $chart_width;?>", "<?php echo $chart_height;?>", 
	"9.0.0", "expressInstall.swf", 
	{"data-file":"<?php echo actionurl('/efass/altgraph/'.$pirep->pirepid);?>"}); 
</script>


<div align="center" style="width: 100%;">
<div align="center" id="speed_chart"></div>
</div>

<script type="text/javascript" src="<?php echo fileurl('/lib/js/ofc/js/swfobject.js')?>"></script>
<script type="text/javascript">
swfobject.embedSWF("<?php echo fileurl('/lib/js/ofc/open-flash-chart.swf');?>", 
	"speed_chart", "<?php echo $chart_width;?>", "<?php echo $chart_height;?>", 
	"9.0.0", "expressInstall.swf", 
	{"data-file":"<?php echo actionurl('/efass/speedgraph/'.$pirep->pirepid);?>"}); 
</script>



<h3>EFASS Flight data records <?php echo $pirep->code . $pirep->flightnum; ?> <?php echo date(DATE_FORMAT, $pirep->submitdate); ?></h3>

<?php
if(!$EFASS)
{
	echo '<p>No EFASS flight data records</p>';
	return;
}
?>
<table id="tabledlist" class="tablesorter">
<thead>
<tr>
	<th>Time</th>
	<th>Stage</th>
	<th>MSL</th>
	<th>Rad Alt.</th>
	<th>IAS</th>
	<th>GS</th>
	<th>Heading</th>
	<th>QNH</th>
	<th>SBC</th>
</tr>
</thead>
<tbody>
<?php
foreach($EFASS as $report)
{
?>
<tr>
	<td align="center"><?php echo $report->time; ?></td>
	<td align="center"><?php echo $report->stage; ?></td>
	<td align="center"><?php echo round($report->alt); ?></td>
	<td align="center"><?php echo round($report->galt); ?></td>
	<td align="center"><?php echo $report->ias; ?></td>
	<td align="center"><?php echo $report->gs; ?></td>
	<td align="center"><?php echo $report->hdg; ?></td>
	<td align="center"><?php echo $report->qnh; ?></td>
	<td align="center"><?php echo $report->sq; ?></td>
</tr>
<?php
}
?>
</tbody>
</table>
