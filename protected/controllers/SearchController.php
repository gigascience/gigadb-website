<?php

class SearchController extends Controller
{
    public $layout='//layouts/column2';

    public function actionEmailNewDatasets() {
        $this->render('emailNewDatasets');
    }

    public function actionEmailMatchedSearches() {
        $this->render('emailMatchedSearches');
    }

    /* return CActiveRecords */
    private function getFullFileResultByKeyword($fileIds) {
        $temp_file_criteria = new CDbCriteria();
        $temp_file_criteria->addInCondition("id", $fileIds);
        return File::model()->findAll($temp_file_criteria);
    }

    private function getFullDatasetResultByKeyword($keyword) {
        $wordCriteria=array();
        $wordCriteria['keyword']=$keyword;
        $list_result_dataset_criteria = $this->searchDataset($wordCriteria);
        $temp_dataset_criteria = new CDbCriteria();
        $temp_dataset_criteria->addInCondition("id", $list_result_dataset_criteria);
        return Dataset::model()->findAll($temp_dataset_criteria);
    }

    public function actionRedirect($id){
        $criteria = SearchRecord::model()->findByPk($id);
        if($criteria==null){
            throw new CHttpException(500,'The requested record does not exist.') ;
        }else {
            $criteria=json_decode($criteria->query,true);
            $params=array();
            $params[]='search/index';
            foreach ($criteria as $key => $value) {
                if(stristr($key, "date")){
                    $params[$key]= str_replace("/", "-", $value);
                }else {
                    $params[$key]=$value;
                }

            }
            $this->redirect($params);
        }
    }

	public function actionSave() {
		$result = array();

		if (Yii::app()->user->isGuest) {
			$result['status'] = "fail";
			$result['reason'] = "You must log in to save search query";
		} else {
			$criteriaStr = $_POST['criteria'];
			$criteria = CJSON::decode($criteriaStr, true);
			if (isset($criteria['keyword']) && strlen($criteria['keyword']) > 0) {
				
				$search = new SearchRecord;
				$search->user_id = Yii::app()->user->_id;
				$search->name = $criteria['keyword'];
				$search->query = $_POST['criteria'];
				$search->result = $_POST['result'];

				if ($search->save()) {
					$result['status'] = "success";
				} else {
					$result['status'] = "fail";
					$result['reason'] = "Unknown Reason";
				}

			} else {
				$result['status'] = "fail";
				$result['reason'] = "Problem with search query, pls check";
			}
		}
		echo json_encode($result);
	}

	public function actionDelete() {
		$id = $_POST['id'];
		$result = array();
		$model = SearchRecord::model()->findByPk($id);
		if ($model) {
			if ($model->user_id == Yii::app()->user->getId()) {
				if ($model->delete()) {
					$result['status'] = "success";
					$result['id'] = $id;
				} else {
					$result['status'] = "fail";
					$result['reason'] = "Unknown Error occur";
				}

			} else {
				$result['status'] = "fail";
				$result['reason'] = "This record does not belongs to you";
			}

		} else {
			$result['status'] = "fail";
			$result['reason'] = "Record Not Found";

		}

		echo json_encode($result);
	}

    
    public function actionNew($keyword = '') {       
        $ds = new DatabaseSearch();        
        $offset = 0;
        $limit = 10;
        $page = 1;
        $data = $ds->searchByKey($keyword);

        if(!Yii::app()->request->isPostRequest) {          
            $datasets = $data['datasets'];
            $datasets['data'] = array_slice($datasets['data'], $offset, $limit);
            $data['datasets'] = $datasets;
            $this->render('new', $data);
        }

        else {            
            try {
                $page = intVal($_POST['page']);
            }
            catch (Exception $e) {
                $page = 1;
            }
            

            $offset = ($page-1)*$limit;
            $datasets = $data['datasets'];
            $datasets['data'] = array_slice($datasets['data'], $offset, $limit);
            $data['datasets'] = $datasets;
            $data['page'] = $page;           

            $result = $this->renderPartial('_new_result', array(
                'model' => $data['model'],
                'datasets' => $data['datasets'],
                'samples' => $data['samples'],
                'files' => $data['files'],
                'display' => $data['display']
            ), true, false); 

            $filter = $this->renderPartial('_new_filter', array(
                'model' => $data['model'],
                'list_dataset_types' => $data['list_dataset_types'],
                'list_projects' => $data['list_projects'],
                'list_ext_types' => $data['list_ext_types'],
                'list_filetypes' => $data['list_filetypes'],
                'list_formats' => $data['list_formats'],
                'list_common_names' => $data['list_common_names']
            ), true, false);

            $range = $this->renderPartial('_range', array(
                            'total_dataset'=>$data['datasets']['total'],
                            'page'=>$data['page'],
                            'limit'=>$data['limit']
                        ), true, false);

            echo CJSON::encode(array('success'=>true, 'filter'=>$filter, 'result'=>$result, 'range'=>$range));
            Yii::app()->end();
        }    
    }

}
