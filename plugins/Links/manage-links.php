<?php
include 'common.php';
include 'header.php';
include 'menu.php';
?>


<div class="main">
    <div class="body container">
        <?php include 'page-title.php'; ?>
        <div class="colgroup typecho-page-main manage-metas">
                <div class="col-mb-12">
                    <ul class="typecho-option-tabs clearfix">
                        <li class="current"><a href="<?php $options->adminUrl('extending.php?panel=Links%2Fmanage-links.php'); ?>"><?php _e('友情链接'); ?></a></li>
						<li><a href=<?php $options->index('/action/links-edit?do=addhanny'); ?> title="如果你喜欢，可以点击快速添加寒泥的博客。如果你也是个人博客，可以到寒泥的博客上与寒泥交换链接"><?php _e('添加寒泥'); ?></a></li>
                        <li><a href="http://www.imhan.com/archives/typecho-links/" title="查看友情链接使用帮助" target="_blank"><?php _e('帮助'); ?></a></li>
                    </ul>
                </div>

                <div class="col-mb-12 col-tb-8" role="main">                  
                    <?php
						$prefix = $db->getPrefix();
						$links = $db->fetchAll($db->select()->from($prefix.'links')->order($prefix.'links.order', Typecho_Db::SORT_ASC));
                    ?>
                    <form method="post" name="manage_categories" class="operate-form">
                    <div class="typecho-list-operate clearfix">
                        <div class="operate">
                            <input type="checkbox" class="typecho-table-select-all" />
                            <div class="btn-group btn-drop">
                                <button class="dropdown-toggle btn-s" type="button" href="">选中项 <i class="i-caret-down"></i></button>
                                <ul class="dropdown-menu">
                                    <li><a lang="<?php _e('你确认要删除这些链接吗?'); ?>" href="<?php $options->index('/action/links-edit?do=delete'); ?>"><?php _e('删除'); ?></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="typecho-table-wrap">
                        <table class="typecho-list-table">
                            <colgroup>
                                <col width="20"/>
								<col width="25%"/>
								<col width=""/>
								<col width="15%"/>
								<col width="10%"/>
                            </colgroup>
                            <thead>
                                <tr>
                                    <th> </th>
									<th><?php _e('链接名称'); ?></th>
									<th><?php _e('链接地址'); ?></th>
									<th><?php _e('分类'); ?></th>
									<th><?php _e('图片'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
								<?php if(!empty($links)): $alt = 0;?>
								<?php foreach ($links as $link): ?>
                                <tr id="lid-<?php echo $link['lid']; ?>">
                                    <td><input type="checkbox" value="<?php echo $link['lid']; ?>" name="lid[]"/></td>
									<td><a href="<?php echo $request->makeUriByRequest('lid=' . $link['lid']); ?>" title="点击编辑"><?php echo $link['name']; ?></a>
									<td><?php echo $link['url']; ?></td>
									<td><?php echo $link['sort']; ?></td>
									<td><?php
										if ($link['image']) {
											echo '<a href="'.$link['image'].'" title="点击放大" target="_blank"><img class="avatar" src="'.$link['image'].'" alt="'.$link['name'].'" width="32" height="32"/></a>';
										} else {
											$options = Typecho_Widget::widget('Widget_Options');
											$nopic_url = Typecho_Common::url('/usr/plugins/Links/nopic.jpg', $options->siteUrl);
											echo '<img class="avatar" src="'.$nopic_url.'" alt="NOPIC" width="32" height="32"/>';
										}
									?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="5"><h6 class="typecho-list-table-title"><?php _e('没有任何链接'); ?></h6></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    </form>
				</div>
                <div class="col-mb-12 col-tb-4" role="form">
                    <?php Links_Plugin::form()->render(); ?>
                </div>
        </div>
    </div>
</div>

<?php
include 'copyright.php';
include 'common-js.php';
?>

<script type="text/javascript">
(function () {
    $(document).ready(function () {
        var table = $('.typecho-list-table').tableDnD({
            onDrop : function () {
                var ids = [];

                $('input[type=checkbox]', table).each(function () {
                    ids.push($(this).val());
                });

                $.post('<?php $options->index('/action/links-edit?do=sort'); ?>', 
                    $.param({lid : ids}));

                $('tr', table).each(function (i) {
                    if (i % 2) {
                        $(this).addClass('even');
                    } else {
                        $(this).removeClass('even');
                    }
                });
            }
        });

        if (table.length > 0) {
            table.tableSelectable({
                checkEl     :   'input[type=checkbox]',
                rowEl       :   'tr',
                selectAllEl :   '.typecho-table-select-all',
                actionEl    :   '.dropdown-menu a'
            });
        } else {
            $('.typecho-list-notable').tableSelectable({
                checkEl     :   'input[type=checkbox]',
                rowEl       :   'li',
                selectAllEl :   '.typecho-table-select-all',
                actionEl    :   '.dropdown-menu a'
            });

            // $('.typecho-table-select-all').click(function () {
            //     var selection = $('.tag-selection');

            //     if (0 == selection.length) {
            //         selection = $('<div class="tag-selection clearfix" />').prependTo('.typecho-mini-panel');
            //     }

            //     selection.html('');

            //     if ($(this).prop('checked')) {
            //         $('.typecho-list-notable li').each(function () {
            //             var span = $('span', this),
            //                 a = $('<a class="button" href="' + span.attr('rel') + '">' + span.text() + '</a>');
                        
            //             this.aHref = a;
            //             selection.append(a);
            //         });
            //     }
            // });
        }

        $('.btn-drop').dropdownMenu({
            btnEl       :   '.dropdown-toggle',
            menuEl      :   '.dropdown-menu'
        });

        $('.dropdown-menu button.merge').click(function () {
            var btn = $(this);
            btn.parents('form').attr('action', btn.attr('rel')).submit();
        });

        // $('.typecho-list-notable li').click(function () {
        //     var selection = $('.tag-selection'), span = $('span', this),
        //         a = $('<a class="button" href="' + span.attr('rel') + '">' + span.text() + '</a>'),
        //         li = $(this);

        //     if (0 == selection.length) {
        //         selection = $('<div class="tag-selection clearfix" />').prependTo('.typecho-mini-panel');
        //     }

        //     if (li.hasClass('checked')) {
        //         this.aHref = a;
        //         a.appendTo(selection);
        //     } else {
        //         this.aHref.remove();
        //     }
        // });

        <?php if (isset($request->lid)): ?>
        $('.typecho-mini-panel').effect('highlight', '#AACB36');
        <?php endif; ?>
    });
})();
</script>
<?php include 'footer.php'; ?>
