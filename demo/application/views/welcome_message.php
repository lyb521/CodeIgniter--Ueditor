
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html>

<?php if($action =='ueditor'){ ?>
<head>
    <title>完整demo</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
    <script type="text/javascript" charset="utf-8" src="/public/ueditor/ueditor.config.js"></script>
    <script type="text/javascript" charset="utf-8" src="/public/ueditor/ueditor.all.min.js"> </script>
    <!--建议手动加在语言，避免在ie下有时因为加载语言失败导致编辑器加载失败-->
    <!--这里加载的语言文件会覆盖你在配置项目里添加的语言类型，比如你在配置项目里配置的是英文，这里加载的中文，那最后就是中文-->
    <script type="text/javascript" charset="utf-8" src="/public/ueditor/lang/zh-cn/zh-cn.js"></script>


</head>
<body>
<div>
    <h1>完整demo</h1>
    <script id="editor" type="text/plain" style="width:1024px;height:500px;"></script>
</div>
<script type="text/javascript">

    //实例化编辑器
    //建议使用工厂方法getEditor创建和引用编辑器实例，如果在某个闭包下引用该编辑器，直接调用UE.getEditor('editor')就能拿到相关的实例
   //var ue = UE.getEditor('editor');
	
	var editor1 = UE.getEditor('editor',{
		serverUrl:'/welcome/ueditor',
		initialFrameWidth:"960",
		initialFrameHeight:"500"
	 });

</script>

<?php }else{?>

<div id="container">
	<h1>Umeditor迷你版</h1>

	<div id="body">
		<script id="content2" name="content2" type="text/plain"></script>
		<!-- 编辑器css -->
	<link href="/public/umeditor/themes/default/css/umeditor.css" type="text/css" rel="stylesheet">
	<!-- jquery -->
	<script type="text/javascript" src="/public/js/jquery.js"></script>
	<!-- 配置文件 -->
	<script type="text/javascript" src="/public/umeditor/umeditor.config.js"></script>
	<!-- 编辑器源码文件 -->
	<script type="text/javascript" src="/public/umeditor/umeditor.min.js"></script>
	<!-- 语言包文件(建议手动加载语言包，避免在ie下，因为加载语言失败导致编辑器加载失败) -->
	<script type="text/javascript" src="/public/umeditor/lang/zh-cn/zh-cn.js"></script>
	<script type="text/javascript">
	var editor = UM.getEditor('content2',{
		imageUrl:'/welcome/ueditor?action=uploadimage',
		imagePath: "",
		initialFrameWidth:"960"
	 });
	</script>
</div>


</div>
<?php }?>


</body>
</html>