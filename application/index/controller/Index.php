<?php
namespace app\index\controller;
//引入自动加载文件
// require 'vendor/autoload.php';
use QL\QueryList;
use think\Db;
// include 'Trans.php';
ini_set("memory_limit","200G");
set_time_limit(0);
ignore_user_abort(); // 后台运行
class Index
{
    public function index()
    {
        //从数据库中查找到所有的slug
        $res = Db::connect('scraper1')->table('brand_list')->field('slug')->select();
        foreach ($res as $key => $value) {
            //拼接所有的URL地址
            $url = 'http://www.nutritionix.com/'.$value['slug'].'/menu/premium';

            $res1 = Db::connect('scraper2')->table('scraper_url')->where("url_name = '$url'")->find();

            if(!$res1){
                $where = [
                    'url_name' => $url,
                ];
                $res2 = Db::connect('scraper2')->table('scraper_url')->insert($where);
                
            }
            
        }

    }

    public function second(){

        //查询url
        $res3 = Db::connect('scraper2')->table('scraper_url')->select();

        // $res3[0] = [

        //     'url_id' => 5,
        //     'url_name' => 'http://www.nutritionix.com/egg-harbor-cafe/menu/premium',
        // ];
        $res3 = array_slice($res3 , 116 , 1);

        foreach ($res3 as $key => $value) {
            dump($key);

            dump($value['url_name']);

            $html = file_get_contents($value['url_name']);
            // die;
            //获取板块
            // $part1 = '/<tr class="subCategory">(.*?)<\/tbody>/is';

            // preg_match_all($part1, $html, $matches1);

            // var_dump($matches1[0]);

            $matches1 = explode('<tr class="subCategory">' , $html);
            unset($matches1[0]);
            // var_dump($matches1);
            foreach ($matches1 as $ke => $val) {
                // var_dump($ke);
                //截取h3  
                $part2 = '/<h3>(.*?)<\/h3>/is';

                preg_match_all($part2, $val, $matches2);
                // var_dump($matches2[0]);

                $subCategory = (trim(strip_tags($matches2[0][0])));
                
                $url_id = $value['url_id'];
                $where2 = [
                    'cat_name' => $subCategory,
                    'urlFK'    => $value['url_id'],
                ];
                $result = Db::connect('scraper2')->table('subcategory')->where($where2)->find();

                if(!$result){

                    Db::connect('scraper2')->table('subcategory')->insert($where2);
                }
                
                //product
                $matches3 = explode('<a class="moreInfo fr"' , $val);

                unset($matches3[0]);
                // var_dump($matches3);
                foreach ($matches3 as $k => $v) {
                    
                    //产品
                    $temp = substr($v , 0 , strrpos($v , '</a>'));
                    
                    $temp = trim(strip_tags($temp));

                    $product_name = substr($temp , (strpos($temp , ']')+1));
                    
                    $subCategory = str_replace("'", "''", $subCategory);
                    // dump($whe);
                    $catFK = Db::connect('scraper2')->table('subcategory')->query("SELECT * FROM `subcategory` WHERE `cat_name` = '{$subCategory}' AND `urlFK` = '{$url_id}' LIMIT 1");

                    if(strpos($subCategory , "''")){
                        $subCategory = str_replace("''", "'", $subCategory);
                        
                    }

                     //id
                    $temp1 = substr($v , 0 , strpos($v , '</a>'));

                    $temp1 = substr($temp1 , (strpos($temp1 , '-')+1));

                    $product_id = substr($temp1 , 0 , strpos($temp1 , '"'));

                    // $catFK = $catFK[0]['id'];
                    $where3 = [
                        'product_name' => $product_name,
                        'catFK'        => $catFK[0]['id'],
                        'detail_id'    => $product_id,
                    ];

                    $result1 = Db::connect('scraper2')->table('product')->where($where3)->find();
                    if(!$result1){

                        Db::connect('scraper2')->table('product')->insert($where3);
                    }else{

                        Db::connect('scraper2')->table('product')->where("detail_id = '$product_id'")->update($where3);
                    }
                }
            }
            // die;
            
        }
    }




}
 

















