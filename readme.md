# 一个基于gd库,方便处理图片的二次封装


### 用法如下

```
$testImg = __DIR__ . '/kobe.jpeg';
$obj     = new ImageHandle($testImg);
$obj->cut(10, 10, 320, 250)->outPut("./kobe1.png"); //裁剪
$obj->radius(20)->outPut("./kobe2.png");//圆角
$obj->rotate(90)->outPut("./kobe3.png");//旋转
$obj->scale(200, 400)->outPut("./kobe4.png");//缩放

```
注意:可以保存任意后缀名图片格式

#### 原图
<img src=https://raw.githubusercontent.com/amzstrong/image/master/kobe.jpeg>


#### 裁剪后图
<img src=https://raw.githubusercontent.com/amzstrong/image/master/kobe1.png>


#### 圆角图
<img src=https://raw.githubusercontent.com/amzstrong/image/master/kobe2.png>



#### 旋转
<img src=https://raw.githubusercontent.com/amzstrong/image/master/kobe3.png>


#### 缩放
<img src=https://raw.githubusercontent.com/amzstrong/image/master/kobe4.png>