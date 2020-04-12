<?php
session_start();
// error_reporting(E_ALL);   //Это тело(или заголовок) ответа. Если включишь - не будет отправляться "добавить статья"
header("Content-Type: text/html; charset=utf-8");
ini_set('display_errors',1);
    // require("auth.php");
    require_once("../database.php");
    require_once("../models/articles.php");

    $link = db_connect();

$article['title']='';       //это, чтобы не выдавало сообщение об ошибке в выводе статей
$article['data']='';
$article['content']='';
$article['author']='';
$article['image']='';

//--------Выход из аккаунта и админики-------//
if($_GET['do'] == 'logout')
{
	unset($_SESSION['login']);
	session_destroy();
  header("Location: /index.php");
  exit;
}

if(!$_SESSION['login'])
{
	header("Location: /index.php?authentication=enter");
	exit;
}
//------------------//


//-------------обработчик изображения----------//
if(!empty($_FILES['upload']['tmp_name']))
  {require_once ("../imageproc.php");}
elseif(!empty($_POST['image']))
  { $image = $_POST['image'];}
//----------/////////


// --------------- логика админки -----------------
//Проверяет, нажата ли какая-либо кнопка действий
if(isset($_GET['action']))
    { $action = $_GET['action']; }
else{ $action ="";}

//add - добавить статью
if($action == "add"){
  if(!empty($_POST['title'])){
    if(empty($_POST['data']))
    {
      $_POST['data'] =  time();
    }
    article_new ($link, $_POST['title'], $_POST['content'], $_POST['data'], $image);
    header ("Location: index.php");
  }
  include("../views/article_admin.php");
}elseif($action == "edit")
{//edit - редактировать статью
  if (!isset($_GET['id']))
  {
    header("Location: index.php");
  };

  $id = (int)$_GET['id'];
  if (!empty($_POST) && $id > 0)
  {
    article_edit($link, $id, $_POST['title'], $_POST['content'], $_POST['data'], $image);
    header("Location: index.php");
  }
  $article = article_get($link, $id);
  include("../views/article_admin.php");
}
elseif ($action == "delete") //удаляем статью
{
  $id = $_GET['id'];
  article_delete($link, $id);
  header("Location: index.php");
}
else{
  $articles = article_all_admin($link);
  include("../views/articles_admin.php");
}

?>
