<?php
	
	/**
	 * 常用基类模型
	 * @author 刘小祥
	 * @date   2015-8-15
	 */
	class CBaseMod extends BaseMod {
		
		public function __construct ( $table ) {
			parent::__construct($table);
		}
		
		public function __get ( $name ) {
			if ( $this->$name ) {
				return $this->$name;
			}
			$append = substr($name , -3);
			if ( $append == "Mod" ) {
				$modName     = substr($name , 0 , -3);
				$this->$name = m($modName);
				
				//$this->$name = new $name();
				return $this->$name;
			}
		}
		
		/**
		 * @todo    获取基础数据方法
		 * @author  Malcolm  (2017年10月10日)
		 * @param int    $id  主键
		 * @param string $key 指定返回的健名称
		 */
		public function getShortInfo ( $id , $key = null ) {
			$info = self::getInfo($id);
			if ( $info['add_time'] ) {
				$info['add_date'] = date('Y-m-d H:i:s' , $info['add_time']);
			}
			
			unset($info['mark']);
			if ( $key !== null ) {
				return $info[$key];
			}
			
			return $info;
		}
		
		/**
		 * 通用编辑方法
		 * @author 刘小祥
		 * @date   2015-8-15
		 */
		public function edit ( $data , $id = 0 , $is_sql = false ) {
			$userinfo = session('userinfo');
			if ( empty($data['upd_time']) ) {
				$data['upd_time'] = time();
			}
			if ( empty($data['upd_user']) ) {
				$data['upd_user'] = (int)$userinfo['id'];
			}
			if ( !$id ) {
				if ( empty($data['add_time']) ) {
					$data['add_time'] = time();
				}
				if ( empty($data['add_user']) ) {
					$data['add_user'] = (int)$userinfo['id'];
				}
			}
			
			
			$this->formatData($data , $id);
			
			if ( $id ) {
				$result = $this->doEdit($id , $data , $is_sql);
				$rowId  = $result ? $id : 0;
			} else {
				$result = $this->doInsert($data , $is_sql);
				$rowId  = $result;
			}
			
			if ( $rowId ) {
				$data['id'] = $rowId;
				$this->_cacheReset($rowId , $data , $id);
			}
			
			return $rowId;
		}
		
		/**
		 * 通用删除方法
		 * @author 刘小祥
		 * @date   2015-8-15
		 */
		public function drop ( $id ) {
			$rs = $this->doMark($id);
			$rs && $this->_cacheDelete($id);
			
			return $rs;
		}
		
		/**
		 * 重置缓存
		 * @author    刘小祥
		 * @param     int $id
		 * @param         array 新数据
		 */
		public function _cacheReset ( $id , $data = [] , $isEdit = true ) {
			if ( !$data ) {
				return $this->resetFuncCache('info' , $id);
			}
			if ( $isEdit ) {
				$info = $this->getFuncCache("info" , $id);
			}
			$info = !empty($info) ? $info : [];
			if ( is_array($data) ) {
				if ( isset($data['table']) ) {
					unset($data['table']);
				}
				$info = array_merge($info , $data);
			} else {
				$info = $data;
			}
			$key = $this->getFuncKey("info" , $id);
			
			return $this->setCache($key , $info);
		}
		
		/**
		 * 获取基本信息
		 * @author 刘小祥
		 * @date   2015-8-15
		 * @return array
		 */
		public function getInfo ( $id ) {
			return $this->getFuncCache("info" , $id);
		}
		
		/**
		 * 获取基本信息
		 * @author 刘小祥
		 * @date   2015-8-15
		 * @return array
		 */
		public function _cacheInfo ( $id ) {
			$info = $this->getRow($id);
			
			return $info;
		}
		
		/**
		 * 通用删除方法
		 * @author 刘小祥
		 * @param int $id
		 */
		public function _cacheDelete ( $id ) {
			$this->deleteFuncCache("info" , $id);
		}
		
		/**
		 * @todo   字段值增加  用于浏览量  等
		 * @author Malcolm  (2018年03月24日)
		 * @param    int $id    主键ID
		 * @param string $field 修改的字段
		 * @param int    $num   操作的值
		 * @return bool
		 */
		public function editColumnValue ( $id , $field = 'view_num' , $num = 1 ) {
			
			$info = $this->getInfo($id);
			if ( $info ) {
				$data = [
					$field => $info[$field] + $num
				];
				
				
				$rs = $this->edit($data , $id);
				if ( $rs ) {
					$this->_cacheReset($id);
				}
			}
			
			return true;
		}
		
		
		/**
		 * join查询总数
		 * @author 刘小祥 (2016年7月4日)
		 * @param mixed $query 形式与getJoinData相同
		 */
		public function getJoinCount ( $query ) {
			$joinSql = "";
			$number  = 98;
			if ( is_array($query['join']) ) {
				foreach ( $query['join'] as $table => $on ) {
					$char    = chr($number++);
					$table   = DB_PREFIX . $table;
					$joinSql .= " LEFT JOIN {$table} AS {$char} ON {$on} ";
				}
			}
			$cond  = is_array($query['cond']) ? implode(" AND " , $query['cond']) : $query['cond'];
			$cond  = "WHERE " . $cond;
			$table = $query['table'] ? DB_PREFIX . $table : $this->table;
			$table = "{$table} AS a";
			$sql   = "SELECT count(*) as num FROM {$table} {$joinSql} {$cond}";
			$data  = $this->getDataBySql($sql);
			
			return $data[0]['num'];
		}
		
		/**
		 * join查询 比getData方法多了一个join
		 * example : $query['join'] = array(
		 *     'user'=>'b.id=a.user_id',
		 *     'agency'=>'b.user_id=b.id'
		 * ) //约定表别名 为 a,b,c......
		 * @author 刘小祥 (2016年5月26日)
		 */
		public function getJoinData ( $query ) {
			$fields  = $query['fields'] ? $query['fields'] : "*";
			$joinSql = "";
			$number  = 98;
			$limit   = $query['limit'] ? "limit " . $query['limit'] : "";
			if ( is_array($query['join']) ) {
				foreach ( $query['join'] as $table => $on ) {
					$char    = chr($number++);
					$table   = DB_PREFIX . str_replace(DB_PREFIX , '' , $table);
					$joinSql .= " LEFT JOIN {$table} AS {$char} ON {$on} ";
				}
			}
			if ( $query['order_by'] ) {
				$orderBy = "ORDER BY {$query['order_by']}";
			}
			$cond  = is_array($query['cond']) ? implode(" AND " , $query['cond']) : $query['cond'];
			$cond  = "WHERE " . $cond;
			$table = $query['table'] ? DB_PREFIX . $table : $this->table;
			$table = "{$table} AS a";
			$sql   = "SELECT {$fields} FROM {$table} {$joinSql} {$cond} {$orderBy} {$limit} ";
			$data  = $this->getDataBySql($sql);
			if ( $query['pri'] ) {
				$newData = [];
				foreach ( $data as $row ) {
					$newData[$query['pri']] = $row;
				}
				
				return $newData;
			}
			
			return $data;
		}
		
		/**
		 * 格式化编辑的数据
		 * @author  刘小祥 (2016年6月2日)
		 * @param array  $data  要格式话的数据
		 * @param int    $id    编号
		 * @param string $table 不带前缀的表名称,默认是当前模型的表 (多个表用逗号分隔开)
		 * @return array 按表的顺序返回一个二维结构的数据
		 * @example list($mainData, $partData) = $this->formatData($data, $id, "user,member");
		 */
		public function formatData ( &$data , $id = 0 , $table = "" ) {
			$dataList = [];
			$tables   = $table ? explode("," , $table) : [ "" ];
			$newData  = [];
			foreach ( $tables as $table ) {
				$tempData      = [];
				$fieldInfoList = $this->getFieldInfoList($table);
				foreach ( $fieldInfoList as $field => $fieldInfo ) {
					if ( $field == "id" ) {
						continue;
					}
					
					//对强制
					if ( isset($data[$field]) ) {
						if ( $fieldInfo['type'] == "int" ) {
							$newData[$field] = (int)$data[$field];
						} else {
							$newData[$field] = (string)$data[$field];
						}
					}
					if ( !isset($data[$field]) && in_array($field , [ 'upd_time' , 'add_time' ]) ) {
						continue;
					}
					//插入数据-设置默认值
					if ( !$id && !isset($data[$field]) ) {
						$newData[$field] = $fieldInfo['default'];
					}
					if ( isset($newData[$field]) ) {
						$tempData[$field] = $newData[$field];
					}
				}
				$dataList[] = $tempData;
			}
			$data = $newData;
			
			return $dataList;
		}
		
		/**
		 * 获取字段信息列表
		 * @author 刘小祥 (2016年6月3日)
		 */
		public function getFieldInfoList ( $table = "" ) {
			$table     = $table ? DB_PREFIX . $table : $this->table;
			$fieldList = $this->getDataBySql("SHOW FIELDS FROM {$table}");
			$infoList  = [];
			foreach ( $fieldList as $row ) {
				if ( ( strpos($row['Type'] , "int") === false ) || ( strpos($row['Type'] , "bigint") !== false ) ) {
					$type    = "string";
					$default = $row['Default'] ? $row['Default'] : "";
				} else {
					$type    = "int";
					$default = $row['Default'] ? $row['Default'] : 0;
				}
				$infoList[$row['Field']] = [
					'type'    => $type ,
					'default' => $default ,
				];
			}
			
			return $infoList;
		}
		
		/**
		 * 延时更新数据库阅读量(指定时间内只更新缓存)
		 * @author 刘小祥 (2016年6月3日)
		 * @
		 */
		public function delayIncrease ( $id , $field = "view_num" , $delay = 60 ) {
			$key       = str_replace(DB_PREFIX , "" , $this->table) . "_{$field}_{$id}";
			$valueInfo = $this->getCache($key);
			if ( !$valueInfo ) {
				$valueInfo['time'] = time();
			}
			
			$num      = (int)$valueInfo['value'] + 1;
			$lastTime = (int)$valueInfo['time'];
			$durTime  = time() - $lastTime;
			$info     = $this->getFuncCache("info" , $id);
			if ( $durTime > $delay ) {
				//过期后,初始化
				$valueInfo['value'] = 0;
				$num                += $info[$field];
				$info[$field]       = $num;
				$rs                 = $this->doEdit($id , [ $field => $num ]);
				$infoKey            = $this->table . "_info_{$id}";
				$this->setCache($infoKey , $info);
				$valueInfo['time'] = time();
			} else {
				//增长缓存中的值
				$valueInfo['value'] = $num;
				$num                += $info[$field];
			}
			$this->setCache($key , $valueInfo);
			
			return $num;
		}
		
		/**
		 * 延时更新阅读量
		 * @author 刘小祥 (2016年6月3日)
		 */
		public function getFieldValue ( $id , $field = "view_num" ) {
			$info      = $this->getFuncCache("info" , $id);
			$key       = str_replace(DB_PREFIX , "" , $this->table) . "_{$field}_{$id}";
			$valueInfo = $this->getCache($key);
			$num       = (int)$valueInfo['value'];
			$num       += $info[$field];
			
			return $num;
		}
		
		/**
		 * 重复性验证
		 * @author zongjl
		 * @date   2017-07-13
		 */
		public function isExist ( $data , $id = 0 , $is_sql = false ) {
			$cond[] = 'mark=1';
			foreach ( $data as $key => $val ) {
				$cond[] = "{$key}='{$val}'";
			}
			if ( $id ) {
				$cond[] = "id!={$id}";
			}
			$query = [
				'fields' => 'id' ,
				'cond'   => $cond ,
			];
			$info  = $this->getOne($query , $is_sql);
			$id    = (int)$info['id'];
			
			return $id;
		}
		
		public function isExistEx ( $data , $id = 0 , $is_sql = false ) {
			foreach ( $data as $key => $val ) {
				$cond[] = "{$key}='{$val}'";
			}
			if ( $id ) {
				$cond[] = "id!={$id}";
			}
			$query = [
				'fields' => 'id' ,
				'cond'   => $cond ,
			];
			$info  = $this->getOne($query , $is_sql);
			$id    = (int)$info['id'];
			
			return $id;
		}
		
	}
