<?php

class  PassportAction extends ActionUtil {

	/**
	 * 收集多玩通行证的信息（接收端）
	 * 运行环境要求：可在外网服务器运行，也可在本地运行
	 * 要求序列化格式：{"imid": "909013545", "uid": "50014545", "register_time": "2014-07-30 12:39:11", "jifen": "70051", "nick": "\u65b9\u5764\u6807|\u6280\u672f|\u591a\u73a9", "passport": "dw_fangkunbiao", "sign": "18029548437"}
	 */
	public function collectDwpassport($u){
		$data = unserialize($_POST['data']);

		if (!is_array($data)) {
			$this->_putjson(array(
				'code' => -1,
				'msg' => '数据非序列化数组格式',
				'extra' => null
			));
		} //END SCRIPT

		$extra = array();
		foreach ($data as $k => $d) {
			if (!is_array($d)) {
				$extra['errformat'][] = $d; //格式错误的数据
				continue;
			}

			$d['yyuid'] = $d['uid'];
			unset($d['uid']);
			
			$pdo = AiMySQL::connect();
			$sth = $pdo->prepare("INSERT INTO dwi_passport(imid, jifen, nick, passport ,register_time, sign, yyuid) values(:imid, :jifen, :nick, :passport, :register_time, :sign, :yyuid)");
			$opt = $sth->execute($d);

			//如果插入不成功，尝试更新（可能存在记录）
			if (false === $opt) {
				$sth = $pdo->prepare("UPDATE dwi_passport SET imid = :imid, jifen = :jifen, nick = :nick, passport = :passport, register_time = :register_time, sign = :sign WHERE yyuid = :yyuid");
				$opt = $sth->execute($d);
			}

			/*$p = new Passport;
			$p->init($d);
			$opt = TableUtil::insert_with_prepare($p);*/

			if (false === $opt) {
				$extra['noinsert'][] = $d; //未成功插入的数据
			} else {
				$extra['inserted'][] = $d; //成功插入的数据
			}
		}

		$this->_putjson(array(
			'code' => 0,
			'msg' => '采集完成',
			'extra' => $extra
		)); //END SCRIPT
	}

	//取view
	public function listDwpassport($u){
		$ret = $this->getPassportsList($u);
		if (-1 == $ret['code']) return;

		$args = array(
			'list' => $ret['data'],
			'fields' => array('imid', 'jifen', 'nick', 'passport', 'register_time', 'sign', 'yyuid')
		);
		$this->tpl($args);
	}

	//取json
	public function getDwPassportJson($u){
		$ret = $this->getPassportsList($u);
		$this->_putjson($ret);
	}


	/**
	 * 收集多玩通行证信息（采集端，负责多玩内网采集，并与接收端通信）
	 * 运行环境：仅多玩内网，不支持在外网运行
	 * 默认一次性采集100个，上限250个
	 */
	public function collectDwpassportOnLocal($u){
		ignore_user_abort();
        set_time_limit(120);    

        $limit = intval($u['params'][1]);
        ($limit <= 0 || $limit > 250) && $limit = 100;

        $catched = array();

        $yyuids = $this->_genRandNumSet($limit);// 50000000 ~ 50100000
        foreach ($yyuids as $yyuid) {
            if (empty($yyuid)) continue;
            $passport = $this->_yyuidGetPassport($yyuid);
            if (false === $passport) continue;
            $catched[] = $passport;
        }

        /*$dws = $this->_genRandNumSet($limit, 909010000, 909090000);// 909010000 ~ 909090000
        foreach ($dws as $dw) {
            if (empty($yyuid)) continue;
            $passport = $this->_dwGetPassport($dw);
            if (false === $passport) continue;
            $catched[] = $passport;
        }*/
        
        $url = 'http://doinject.duapp.com/APPS/duowaninfo/?x=collectDwpassport';
        //$url = 'http://172.16.43.185:8080/doinject_bae/APPS/duowaninfo/?x=collectDwpassport';
        $reportResult = $this->_reportToPubSite($url, $catched);//暂存

        $this->_putjson(compact('catched', 'reportResult'));
	}



    //get array <Passport> 根据yyuid取整个通行证信息
    private function _yyuidGetPassport($yyuid){
        $url = 'http://172.19.102.25:8080/webdb/query_userinfo?';
        $url .= http_build_query(array(
            'type' => 3,
            'value' => $yyuid,
            '_r' => '0.'.$this->_genRandNums(16),
        ));
        
        $dh = new dwHttp();
        $ret = json_decode($dh->get($url));
        
        return isset($ret->info) ? (array)$ret->info : false;        
    }

    //get array <Passport> 根据dwpassport取整个通行证信息
    private function _dwGetPassport($dw){
        $url = 'http://172.19.102.25:8080/webdb/query_userinfo?';
        $url .= http_build_query(array(
            'type' => 1,
            'value' => $dw,
            //'_r' => '0.'.$this->_genRandNums(16),
        ));
        
        $dh = new dwHttp();
        $ret = json_decode($dh->get($url));
        
        return isset($ret->info) ? (array)$ret->info : false;        
    }

    //将数据暂存到临时存储点
    private function _reportToPubSite($url, $data){
        $match = preg_match('/((http|https):\/\/)?([a-z0-9_\-\/\.]+\.[][a-z0-9:;&#@=_~%\?\/\.\,\+\-]+)/', $url);
        if (!$match) return array('status' => false);

        $data = serialize($data);
        $dh = new dwHttp();
        $ret = $dh->post($url, compact('data'));
        $ret = json_decode($ret);
        
        return array('status' => true, 'ret' => $ret);
    }

	private function getPassportsList($u){
		$limit = intval($u['params'][1]);
		$limit <= 0 && $limit = 10;
		$pdo = AiMySQL::connect();
		$sth = $pdo->query("SELECT * FROM dwi_passport limit {$limit}", PDO::FETCH_CLASS, 'Passport');
		$count = $sth->rowCount();
		if (empty($count)) return array('code' => -1, 'msg' => '没数据');

		$objs = array();
		foreach ($sth as $s) {
			$objs[] = $s;
		}
		
		return array('code' => 0, 'msg' => '获取成功' , 'data' => $objs);
	}

	private function obj2arr($obj) {
	    if(is_object($obj)) {
	        $obj = (array)$obj;
	        $obj = ob2ar($obj);
	    } elseif(is_array($obj)) {
	        foreach($obj as $key => $value) {
	            $obj[$key] = ob2ar($value);
	        }
	    }
	    return $obj;
	}

    //随机n位数
    private function _genRandNums($n){
        $m = '';
        while ($n > 0 && $n--){
            $m .= mt_rand(0, 9);
        }
        return $m;
    }

    //生成随机数集合：$n 个数，$dupl 允许重复，包含$min和$max
    private function _genRandNumSet($n, $min = 50000000, $max = 50100000, $dupl = false){
    	$ret = array();
    	while ($n > 0 && $n --) {
    		REDO:
    		$tmp = mt_rand($min, $max);
    		if (in_array($tmp, $ret)) goto REDO;
    		array_push($ret, $tmp);
    	}
    	return $ret;
    }

	private function _putjson(array $feed){
		echo json_encode($feed);
		exit;
	}

}

?>