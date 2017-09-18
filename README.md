Ueditor-CodeIgniter（ci）插件

================

Ueditor for CodeIgniter（ci）插件

根据最新的CodeIgniter编写，支持Ueditor和umeditor两个版本编辑器，具体配置可参考demo里面。

### 使用说明

一、安装：

1、将Ueditor application 与你的ci项目合并，如果有相同的文件命名，请自行修改
	
	文件说明：
		application/confi/upload.php  上传配置
		application/libraries/Ueditor.php  Ueditor扩展类
		
	将这两个文件放到相应的目录即可

2、将public目录放到项目根目录（如果你的项目目录中没有public，放其目录也可以）
	
	文件说明：
		public/ueditor  Ueditor完整版包
		public/umeditor  umeditor迷你版包
		public/js  	umeditor包的依赖jquery
	
3、uploads为上传目录，可以在uploads中进行配置，根据自己项目确定

二、使用：

1、在控制器中添加ueditor方法

```php

	/**
	 * 百度编辑器
	 */
	public function ueditor(){
		
		$this->load->library('Ueditor');
		
		echo $this->ueditor->output_data();
		
	}
	
```
2、 添加以下代码到你视图的view文件

```javascript
	<!--完整版-->
		<script type="text/javascript" charset="utf-8" src="/public/ueditor/ueditor.config.js"></script>
		<script type="text/javascript" charset="utf-8" src="/public/ueditor/ueditor.all.min.js"> </script>
		<!--建议手动加在语言，避免在ie下有时因为加载语言失败导致编辑器加载失败-->
		<!--这里加载的语言文件会覆盖你在配置项目里添加的语言类型-->
		<script type="text/javascript" charset="utf-8" src="/public/ueditor/lang/zh-cn/zh-cn.js"></script>
		<script id="editor" type="text/plain" style="width:1024px;height:500px;"></script>
		<script type="text/javascript">

		//实例化编辑器
		var editor1 = UE.getEditor('editor',{
			serverUrl:'/welcome/ueditor',
			initialFrameWidth:"960",
			initialFrameHeight:"500"
		 });

	</script>
	
	<!--Umeditor迷你版-->
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
	
```
三、联系

    代码还在不断完善中，有任何意见和建议：
   
	个人主页：[https://www.leiyongbo.com](https://www.leiyongbo.com)  
	
	github开源: https://github.com/lyb521
