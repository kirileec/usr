<?php
/**
 * 这是基于 Typecho 默认皮肤进行修改的皮肤，什么时候都不知道是什么样子
 * 
 * @package Typecho Modified Theme 
 * @author kirile
 * @version 0.1
 * @link http://kirile.gq
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
 
$this->
need('header.php');
 ?>

<div class="col-mb-12 col-8" id="main" role="main">

<!--开始列表循环-->
  <?php while($this->
  next()): ?>
  <!--定义一个POST-->
  <article class="post" itemscope itemtype="http://schema.org/BlogPosting">
    <!--标题-->
    <h2 class="post-title" itemprop="name headline">
      <a itemtype="url" href="<?php $this->
        permalink() ?>">
        <?php $this->title() ?></a>
    </h2>
    <!--标题 end-->
    <!--POST相关信息-->
    <ul class="post-meta">

      <li itemprop="author" itemscope itemtype="http://schema.org/Person">
        <?php _e('作者: '); ?>
        <a itemprop="name" href="<?php $this->
          author->permalink(); ?>" rel="author">
          <?php $this->author(); ?></a>
      </li>

      <li>
        <?php _e('时间: '); ?>
        <time datetime="<?php $this->
          date('c'); ?>" itemprop="datePublished">
          <?php $this->date('F j, Y'); ?></time>
      </li>

      <li>
        <?php _e('分类: '); ?>
        <?php $this->category(','); ?></li>
      <li itemprop="interactionCount">
        <a itemprop="discussionUrl" href="<?php $this->
          permalink() ?>#comments">
          <?php $this->commentsNum('评论', '1 条评论', '%d 条评论'); ?></a>
      </li>

    </ul>
    <!--POST相关信息 end-->
    <div class="post-content" itemprop="articleBody">
      <?php $this->content('- 阅读剩余部分 -'); ?>
    </div>
  </article>
  <!--定义一个POST end-->
  <?php endwhile; ?>
  <!--结束循环-->

  <?php $this->pageNav('&laquo; 上一页', '下一页 &raquo;'); ?></div>
<!-- end #main-->

<?php $this->
need('sidebar.php'); ?>
<?php $this->
need('footer.php'); 

?>