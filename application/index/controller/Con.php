<?php
namespace app\index\controller;
//引入自动加载文件
// require 'vendor/autoload.php';
use QL\QueryList;
use think\Db;
include "../ORG/LIB_http.class.php";
include "../ORG/LIB_parse.class.php";
include "../ORG/LIB_download_images.class.php";
header('Content-Type:text/html;charset=utf-8');
ini_set("memory_limit","200G");
set_time_limit(0);
ignore_user_abort(); // 后台运行
class Con
{
     public function third(){

        $result = Db::connect('scraper2')->table('product')->select();

        $result = array_slice($result , 58400 , 10000);

        // dump($result);
        // die;

        foreach ($result as $key => $value) {

            //拼接URL地址
            $detail_id = $value['detail_id'];

            $catFK = $value['catFK'];

            $result1 = Db::connect('scraper2')->table('subcategory')->field('urlFK')->where("id = $catFK")->find();

            $url_id = $result1['urlFK'];

            $url_name = Db::connect('scraper2')->table('scraper_url')->field('url_name')->where("url_id = $url_id")->find();

            // var_dump($url_name);
            $detail_url_temp = substr($url_name['url_name'] , 0 , strrpos($url_name['url_name'] , '/'));

            $detail_url = str_replace('menu' , 'viewLabel/' , $detail_url_temp);

            $detail_id = str_replace('-' , '/' , $detail_id);
            
            $url = $detail_url.$detail_id;

            // $url = 'http://www.nutritionix.com/pizza-hut/viewLabel/ingredient/23485';
            dump($url);
            //获取html
            $head = array(
               "Accept: */*",
               'Accept-Encoding: gzip, deflate',
               'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 Safari/537.36',
            );
            $file = http($url, '', GET, '', '', '', 0, $head);
            $res = gzdecode($file['FILE']);
            
            $result = parse_array($res, "<div", "</script>");
            // dump($result);
            $html = $result[0];

            //获取allergen_info
            $allergen_info = '';

            $part1 = '/<tr>(.*?)<\/tr>/is';

            preg_match_all($part1, $html, $matches1);

            if(!empty($matches1[0])){

                foreach ($matches1[0] as $ke => $val) {
                    // dump(trim(strip_tags($val)));

                    $allergen_temp = trim(strip_tags($val));

                    // dump($allergen_temp);

                    if(strpos($allergen_temp , 'NOT PREPARED USING THE CERTIFIED GIG PROCESS')){

                        $allergen_temp = trim(substr($allergen_temp , 0 , strpos($allergen_temp , 'NOT PREPARED USING THE CERTIFIED GIG PROCESS'))).'('.'NOT PREPARED USING THE CERTIFIED GIG PROCESS'.')';

                        // dump($allergen_temp);
                    }

                    if(strpos($allergen_temp , '!')){

                        $allergen_temp = trim(substr($allergen_temp , 0 , strpos($allergen_temp , '!'))).' '.'1';

                    }else{
                        $allergen_temp = trim($allergen_temp).' '.'0';

                    }

                    $allergen_info .= $allergen_temp.',';

                    
                }
                // dump($allergen_info);
                
            }else{

                $allergen_info = '';
            }
            
            //获取ingredients
            $part = '/<div class="weight" style="font-weight: normal;">(.*?)<\/div>/is';

            preg_match_all($part, $html, $matches);

            // dump($matches[0][0]);
            if(!empty($matches[0])){

                $ingredients = trim(strip_tags($matches[0][0]));

                // dump($ingredients);

            }else{
                $ingredients = '';
            }

            $html1 = explode('//these are the default values for the nutrition info' , $html);

            $html1[1] = substr($html1[1] , 0 , strpos($html1[1] , '}'));

            $va = explode(',' , $html1[1]);

            $arr = array();

            foreach ($va as $k => $v) {
                
                $value_key = trim(substr($v , 0 , strpos($v , ':')));

                $value_val = trim(substr($v , (strpos($v , ':')+1)));

                $arr[$value_key] = $value_val;
            }

            $data = [
                'product_id' => $value['id'],
                'allergen_info' => $allergen_info,
                'ingredients'   => $ingredients,
                'valueServingSizeUnit' => $arr['valueServingSizeUnit'],
                'valueServingPerContainer' => $arr['valueServingPerContainer'],
                'valueServingUnitQuantity' => $arr['valueServingUnitQuantity'],
                'valueCalories'  => $arr['valueCalories'],
                'valueFatCalories'  => $arr['valueFatCalories'],
                'valueTotalFat'     => $arr['valueTotalFat'],
                'valueSatFat'       => $arr['valueSatFat'],
                'valueTransFat'     => $arr['valueTransFat'],
                'valuePolyFat'      => $arr['valuePolyFat'],
                'valueMonoFat'      => $arr['valueMonoFat'],
                'valueCholesterol'  => $arr['valueCholesterol'],
                'valueSodium'       => $arr['valueSodium'],
                'valueTotalCarb'    => $arr['valueTotalCarb'],
                'valueFibers'       => $arr['valueFibers'],
                'valueSugars'       => $arr['valueSugars'],
                'valueSugarAlcohol' => $arr['valueSugarAlcohol'],
                'valueProteins'     => $arr['valueProteins'],
                'valueVitaminA'     => $arr['valueVitaminA'],
                'valueVitaminC'     => $arr['valueVitaminC'],
                'valueCalcium'      => $arr['valueCalcium'],
                'valueIron'         => $arr['valueIron'],
                'valuePotassium_2018' => $arr['valuePotassium_2018'],
                'valueVitaminD'     => $arr['valueVitaminD'],
                'valueAddedSugars'  => $arr['valueAddedSugars'],
                'valueServingWeightGrams' => $arr['valueServingWeightGrams'],

            ];

            $where = [
                'product_id' => $value['id'],
                // 'allergen_info' => $allergen_info,
                // 'ingredients'   => $ingredients,
            ];

            dump($where);
            $result2 = Db::connect('scraper2')->table('detail_copy')->where($where)->find();

            if(!$result2){

                Db::connect('scraper2')->table('detail_copy')->insert($data);
            }else{

                Db::connect('scraper2')->table('detail_copy')->where($where)->update($data);
            }
            // die;
            

        }
        
    }

}
 

















