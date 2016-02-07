<?php
/**
 * 蜘蛛来访日志查看
 * 
 * @package Robots
 * @author Shion
 * @version 3.0.0
 * @update: 2011.11.05
 * @link http://www.shionco.com
 */
include 'common.php';
include 'header.php';
include 'menu.php';
?>
<div class="main">
    <div class="body body-950">
		<?php include 'page-title.php'; ?>
        <div class="container typecho-page-main">
            <div class="column-24 start-01 typecho-list">
                <div class="typecho-list-operate">
                    <?php
						$config  = Typecho_Widget::widget('Widget_Options')->plugin('Robots');
						$botlist = $config->botlist;
						$pagecount = $config->pagecount;
						$isdrop = $config->droptable;
						if ($botlist == null || $pagecount == null || $isdrop == null)
						{
							throw new Typecho_Plugin_Exception('请先设置插件！');
						}
						$db = Typecho_Db::get();
						$prefix = $db->getPrefix();
						$p = 1;
						$rtype = '';
						$oldtype = '';
						if (isset($_POST['rpage'])) {
							$p = $_POST['rpage'];
						}
						if (isset($_POST['do'])) {
							$do = $_POST['do'];
							if ($do == 'delete') {
								if (isset($_POST['lid'])) {
									$lids = $_POST['lid'];
									$deleteCount = 0;
									if ($lids && is_array($lids)) {
										foreach ($lids as $lid) {
											if ($db->query($db->delete($prefix.'logs')->where('lid = ?', $lid))) {
												$deleteCount ++;
											}
										}
									}
									Typecho_Widget::widget('Widget_Notice')->set('成功删除蜘蛛日志',NULL,'success');
									Typecho_Response::redirect(Typecho_Common::url('extending.php?panel=Robots%2FLogs.php', $options->adminUrl));
								}else{
									Typecho_Widget::widget('Widget_Notice')->set('当前没有选择的日志',NULL,'notice');
									Typecho_Response::redirect(Typecho_Common::url('extending.php?panel=Robots%2FLogs.php', $options->adminUrl));
								}
							}
							if (strpos($do,'clear')!==false)
							{
								try
								{
									$cleartype = substr($do, 6);
									$options = Typecho_Widget::widget('Widget_Options');
									$timeStamp = $options->gmtTime;
									$offset = $options->timezone - $options->serverTimezone;
									$gtime = $timeStamp + $offset;
									$lowtime = $gtime - ($cleartype * 86400);
									$db->query($db->delete($prefix.'logs')->where('ltime < ?', $lowtime));
									Typecho_Widget::widget('Widget_Notice')->set('清除日志成功',NULL,'success');
									Typecho_Response::redirect(Typecho_Common::url('extending.php?panel=Robots%2FLogs.php', $options->adminUrl));
								}
								catch (Typecho_Db_Exception $e)
								{
									Typecho_Widget::widget('Widget_Notice')->set('清除日志失败',NULL,'notice');
									Typecho_Response::redirect(Typecho_Common::url('extending.php?panel=Robots%2FLogs.php', $options->adminUrl));
								}
							}
						}
						if (isset($_POST['oldtype'])) {
							$oldtype = $_POST['oldtype'];
						}
						if (isset($_POST['rpage']) && $_POST['rtype']!=='') {
							$rtype = $_POST['rtype'];
							if ($oldtype !== $rtype) {
								$p = 1;
							}
							$logs = $db->fetchAll($db->select()->from($prefix.'logs')->where('bot = ?', $rtype)->order($prefix.'logs.lid', Typecho_Db::SORT_DESC)->page($p, $pagecount));
							$rows = count($db->fetchAll($db->select('lid')->from($prefix.'logs')->where('bot = ?', $rtype)));
						}else{
							$logs = $db->fetchAll($db->select()->from($prefix.'logs')->order($prefix.'logs.lid', Typecho_Db::SORT_DESC)->page($p, $pagecount));
							$rows = count($db->fetchAll($db->select('lid')->from($prefix.'logs')));
						}
						$co = $rows % $pagecount;
						$pageno = floor($rows / $pagecount);
						if ($co !== 0) {
							$pageno += 1;
						}
                    ?>
                <form method="post" action="<?php $options->adminUrl('extending.php?panel=Robots%2FLogs.php'); ?>">
                    <p class="operate">操作: 
                        <span class="operate-button typecho-table-select-all">全选</span>, 
                        <span class="operate-button typecho-table-select-none">不选</span>&nbsp;&nbsp;&nbsp;
                        选中项: 
                        <span rel="delete" lang="你确认要删除这些日志吗?" class="operate-button operate-delete typecho-table-select-submit">删除</span>&nbsp;&nbsp;&nbsp;
						清除选项:
						<span rel="clear_0" lang="你确认要清除所有的日志吗?" class="operate-button operate-clear typecho-table-select-submit">清除所有</span>
						<span rel="clear_1" lang="你确认要只保留一天内的日志吗?" class="operate-button operate-clear typecho-table-select-submit">保留一天</span>
						<span rel="clear_2" lang="你确认要只保留两天内的日志吗?" class="operate-button operate-clear typecho-table-select-submit">保留两天</span>
						<span rel="clear_3" lang="你确认要只保留三天内的日志吗?" class="operate-button operate-clear typecho-table-select-submit">保留三天</span>
						<span rel="clear_7" lang="你确认要只保留一周内的日志吗?" class="operate-button operate-clear typecho-table-select-submit">保留一周</span>
						<span rel="clear_15" lang="你确认要只保留半个月内的日志吗?" class="operate-button operate-clear typecho-table-select-submit">保留半个月</span>
						<span rel="clear_30" lang="你确认要只保留一个月内的日志吗?" class="operate-button operate-clear typecho-table-select-submit">保留一个月</span>
                    </p>
                    <p class="search">
                    <select name="rpage">
                        <?php for ($i = 1; $i <= $pageno; $i++): ?>
                    	<option value="<?php echo $i ?>" <?php if ($i == $p): ?>selected="selected"<?php endif; ?>>第<?php echo $i ?>页</option>
                        <?php endfor; ?>
                    </select>
                    <select name="rtype">
                    	<option value="">所有蜘蛛</option>
                        						<?php
												if (sizeof($botlist)>0) {
													foreach ($botlist as $bot) {
														$selected = $rtype==$bot ? ' selected="selected"' : NULL;
														echo '<option value="'.$bot.'"'.$selected.'>'.botname($bot).'</option>';
													}
												}
												function botname($bot)
												{
													switch ($bot) {
														case "baidu":
															return '百度';
															break;
														case "google":
															return '谷歌';
															break;
														case "yahoo":
															return '雅虎';
															break;
														case "sogou":
															return '搜狗';
															break;
														case "youdao":
															return '有道';
															break;
														case "soso":
															return '搜搜';
															break;
														case "bing":
															return '必应';
															break;
													}
												}
												?>
                    </select>
                    <button type="submit">查看</button>
                                        </p>
                <input type="hidden" name="do" value="select" />
                <input type="hidden" name="oldtype" value="<?php echo $rtype; ?>" />
                </form>
                </div>
            
                <form method="post" action="<?php $options->adminUrl('extending.php?panel=Robots%2FLogs.php'); ?>">
                <table class="typecho-list-table">
                    <colgroup>
                        <col width="25"/>
                        <col width="50"/>
                        <col width="260"/>
                        <col width="60"/>
                        <col width="30"/>
                        <col width="110"/>
                        <col width="205"/>
                        <col width="150"/>
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="typecho-radius-topleft"> </th>
                            <th> </th>
                            <th>受访地址</th>
                            <th> </th>
                            <th> </th>
                            <th>蜘蛛名称</th>
                            <th>IP地址</th>
                            <th class="typecho-radius-topright">日期</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(!empty($logs)): ?>
                    <?php foreach ($logs as $log): ?>
						<tr class="even" id="post-5">
							<td><input type="checkbox" value="<?php echo $log['lid']; ?>" name="lid[]"/></td>
                            <td></td>
                            <td colspan="2"><a href="<?php echo str_replace("%23", "#", $log['url']); ?>"><?php echo urldecode(str_replace("%23", "#", $log['url'])); ?></a></td>
                            <td></td>
                            <td><?php echo botname($log['bot']); ?></td>
                            <td><?php echo $log['ip']; ?></td>
                            <td><?php echo date('Y-m-d H:i:s',$log['ltime']); ?></td>
                        </tr>
					<?php endforeach; ?>
                    <?php else: ?>
                    <tr class="even">
                        <td colspan="8"><h6 class="typecho-list-table-title"><?php _e('当前无蜘蛛日志'); ?></h6></td>
                    </tr>
                    <?php endif; ?>
					</tbody>
                </table>
                <input type="hidden" name="do" value="delete" />
                </form>
            </div>
        </div>
    </div>
</div>
<?php
include 'copyright.php';
include 'common-js.php';
?>
<?php include 'footer.php'; ?>
