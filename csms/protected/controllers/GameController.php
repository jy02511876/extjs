<?php
class GameController extends Controller
{
	public function actionAll()
	{
		$data = BasicGame::model()->getGames();
		$rows = array();
		foreach($data as $value)
			$rows[] = array('game_code'=>$value->game_code,'game_name'=>$value->game_name);
		
		echo json_encode(array('rows'=>$rows));
	}
}