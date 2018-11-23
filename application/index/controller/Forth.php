<?php
namespace app\index\controller;
//引入自动加载文件
// require 'vendor/autoload.php';
use QL\QueryList;
use think\Db;
// include 'Trans.php';
ignore_user_abort(); // 后台运行
class Forth
{
     public function forth(){

        die;

        $result = Db::connect('scraper2')->table('product')->select();

        // dump($result);

        foreach ($result as $key => $value) {

            $detail_id = $value['detail_id'];

            $catFK = $value['catFK'];

            $result1 = Db::connect('scraper2')->table('subcategory')->field('urlFK , id')->where("id = $catFK")->find();

            $url_id = $result1['urlFK'];

            $url_name = Db::connect('scraper2')->table('scraper_url')->field('url_name')->where("url_id = $url_id")->find();

            // var_dump($url_name);
            $detail_url_temp = substr($url_name['url_name'] , 0 , strrpos($url_name['url_name'] , '/'));

            $detail_url = str_replace('menu' , 'viewLabel/' , $detail_url_temp);

            $detail_id = str_replace('-' , '/' , $detail_id);
            
            $url = $detail_url.$detail_id;
            // dump($url);

            // $url = 'http://www.nutritionix.com/kfc/viewLabel/ingredient/23948';

            $html = file_get_contents($url);

            // dump($html);
            $allergen_info = '';

            $part1 = '/<tr>(.*?)<\/tr>/is';

            preg_match_all($part1, $html, $matches1);

            if(!empty($matches1[0])){

                foreach ($matches1[0] as $ke => $val) {
                    // dump(trim(strip_tags($val)));

                    $allergen_temp = trim(strip_tags($val));

                    if(strpos($allergen_temp , '!')){

                        $allergen_temp = trim(substr($allergen_temp , 0 , strpos($allergen_temp , '!'))).' '.'1';

                    }else{
                        $allergen_temp = trim(strip_tags($val)).' '.'0';

                    }

                    $allergen_info .= $allergen_temp.',';

                    
                }
                // dump($allergen_info);
                
            }else{

                $allergen_info = '';
            }
            

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

            // dump($arr);
            // dump($value);

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
                'allergen_info' => $allergen_info,
                'ingredients'   => $ingredients,
            ];

            $result2 = Db::connect('scraper2')->table('detail')->where($where)->find();

            if(!$result2){

                Db::connect('scraper2')->table('detail')->insert($data);
            }else{

                Db::connect('scraper2')->table('detail')->where($where)->update($data);
            }

            

        }
        
    }

}
 

















