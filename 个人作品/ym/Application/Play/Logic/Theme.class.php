<?php
namespace Play\Logic;
use Think\Model;

class Theme extends Model {

    /**
     * 
     * @return type
     */
    public function ThemeListCache($theme) {
        //设置缓存 -> 判断缓存 -> 有 -> 输出缓存
        //                    -> 无 -> 查数据库 -> 设置缓存
        $themeCache = "";
        if($theme){
            $where["id"] = $theme;
        }
       // $themeCache = S('themeList'); 
        if (empty($themeCache)) {
            $themeData = D('Play/PlayTheme');
            $themeList = $themeData->getData($where);
            shuffle($themeList);
         //   S('themeList', $themeList, 3000);
            return $themeList;
        } else {
            return $themeCache;
        }
    }

}
