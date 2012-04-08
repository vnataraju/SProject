<?php
function showBlogs()
{
	global $_MY_CONFIG;

	$db		=& JFactory::getDBO();

	require_once( MY_COM_PATH . DS . "functions.myblog.php");
	require_once( MY_ADMIN_COM_PATH . DS . "config.myblog.php");

	$_MY_CONFIG = new MYBLOG_Config();
	$mainframe	=& JFactory::getApplication();
	
	$limit 		= intval( $mainframe->getUserStateFromRequest( "viewlistlimit",'limit',$mainframe->getCfg('list_limit') ) );
	$limitstart = intval( $mainframe->getUserStateFromRequest( "view{com_myblog}limitstart",'limitstart',0 ) );
	$search 	= $mainframe->getUserStateFromRequest("search{com_myblog}", 'search', '');
	$search 	= $db->getEscaped(trim(strtolower($search)));

	$publish = "";

	if (isset ($_GET['publish']))
		$publish = intval($_GET['publish']);

	$where = array ();
	if ($search)
	{
		$where[] = "LOWER(comment) LIKE '%$search%'";
	}

	if(is_int($publish))
	{
		$where[] = "STATE=$publish";
	}
	$where[] = (JVERSION >= 1.6) 
			? "catid IN (" . $_MY_CONFIG->get('managedSections') . ")"
			: "sectionid IN (" . $_MY_CONFIG->get('managedSections') . ")";

	$db->setQuery("SELECT count(*) FROM #__content" . (count($where) ? "\nWHERE " . implode(' AND ', $where) : ""));
	$total = $db->loadResult();
	

	$pagination	= myPagination($total, $limitstart , $limit);
	
	$limitQuery	=  '';
	
	if( $pagination->limit != 0 )
	{
		$limitQuery	=  "\nLIMIT $pagination->limitstart,$pagination->limit";
	}
	
	
	$db->setQuery("SELECT * FROM #__content a LEFT JOIN #__myblog_entry_attr b ON a.id=b.contentid " . (count($where) ? "\nWHERE " . implode(' AND ', $where) : "") . "\n ORDER BY id DESC " . $limitQuery );
	$rows = $db->loadObjectList();
	$total = count($rows);

	$user	=& JFactory::getUser();
	
	$strSQL	= "SELECT session_id FROM #__session WHERE `userid`={$user->id}";
	$db->setQuery( $strSQL );

	$userId		= $user->id;
	$sessionId	= $db->loadResult();
?>
	<script type="text/javascript" language="javascript">
	function toggleEntry(id){
		var entry	= document.getElementById(id + '_content');
		
		if(entry.style.display == 'none')
			entry.style.display	= '';
		else
			entry.style.display = 'none';
	}
	</script>

<form action="index.php?<?php echo htmlspecialchars($_SERVER['QUERY_STRING'],ENT_QUOTES); ?>" method="POST" name="adminForm">
	<div id="viewOption">
		<ul id="navigation">
		<li><a <?php if(!isset($_GET['publish'])): ?>class="active"<?php endif; ?> href="index.php?option=com_myblog&task=blogs&limitstart=0">view all</a></li>
		<li><a <?php if(isset($_GET['publish']) && $_GET['publish']==1): ?>class="active"<?php endif; ?> href="index.php?option=com_myblog&task=blogs&limitstart=0&publish=1">view published</a></li>
		<li><a <?php if(isset($_GET['publish']) && $_GET['publish']==0): ?>class="active"<?php endif; ?> href="index.php?option=com_myblog&task=blogs&limitstart=0&publish=0">view unpublished</a></li>
		</ul>
	</div>

	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="adminlist" >
		<thead>
		<tr>
			<th width="2%">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" />
			</th>
			<th width="5%" class="title" >status</th>
			<th width="50%" class="title" >title</th>
			<th width="40%" class="title" >tag</th>

		</tr>
		</thead>
		<tbody>
		<?php
			$i=0;$n=1;
			if ($rows) {
				foreach($rows as $row){
					$task=$row->state ? 'unpublish':'publish';
						//$img=$row->state ? 'publish_g.png':'publish_x.png';
					$statusAlt = $row->state ? 'published' : 'unpublished';
					$img	= $row->state ?  'components/com_myblog/assets/images/published.png' : 'components/com_myblog/assets/images/unpublished.png';
		?>
		<tr>
			<td>
				<input type='checkbox' id='cb<?php echo $i;?>' name='cid[]' value='<?php echo $row->id;?>' onclick='isChecked(this.checked);' />
			</td>
			
			<td class="imgStatus">
			<a href="javascript:void(0);" onclick="jax.icall('myblog','myxTogglePublishAdmin','<?php echo $row->id;?>');">
			<img id="pubImg<?php echo $row->id;?>" src="<?php echo $img;?>" border="0" alt="<?php echo $statusAlt; ?>" title="<?php echo $statusAlt; ?>" /></a>
			</td>

			<td>
				<span class="postTitle"><?php echo htmlspecialchars($row->title); ?></span><br/>
				<span class="postBy">
				<img class="icon" src="components/com_myblog/assets/images/writer.png"> <?php echo myUserGetName($row->created_by);?>&nbsp;&nbsp;
				<img class="icon" src="components/com_myblog/assets/images/time.png"> <?php echo JHTML::_('date', $row->created);?>&nbsp;&nbsp;
<?php if($row->is_quickpost) :?><img class="icon" src="components/com_myblog/assets/images/quickpost.png"><?php echo ucfirst($row->quickpost_type); ?> quickpost<?php endif; ?>
</span>
				<br/>
				<span class="postAction">
				<a href="javascript:void(0);" onclick="toggleEntry('<?php echo $row->id; ?>');">Preview</a>
				<a href="javascript:void(0);" onclick="myAzrulShowWindow('<?php echo rtrim( JURI::root() ,'/');?>/index.php?option=com_myblog&task=write&no_html=1&tmpl=component&id=<?php echo $row->id;?>&session=<?php echo $userId . ':' . $sessionId;?>');">Edit</a>
				</span>
			</td>

			<td><span class="postTag"><?php echo myCategoriesURLGet($row->id,false,false,false);?></span></td>
		
		</tr>

		<tr id="<?php echo $row->id; ?>_content" style="display: none;">
			<td colspan="7"><?php echo $row->introtext . $row->fulltext; ?></td>
		</tr>

		<?php $i++;
			}
		}
		?>
		</tbody>
	<tfoot>
		<tr>
		<td colspan="7">
		<input type="hidden" name="option" value="com_myblog" />
		<input type="hidden" name="task" value="blogs" />
		<input type="hidden" name="boxchecked" value="0" />
		<div style="width:100%;"><?php echo $pagination->footer;?></div>
		</td>
		<tr>
	</tfoot>
	</table>
</form>

<!-- MAIN CONTENT END -->

<?php
}
