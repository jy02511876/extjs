<?php
class Logger {
	private static $_logger;
	
	public static function log($msg,$path,$file='application.log')
	{
		if(!isset(self::$_logger[$file])  || self::$_logger[$file] === null){
			self::$_logger[$file] = new FileLog();
			self::$_logger[$file]->setLogPath($path);
			self::$_logger[$file]->setLogFile($file);
		}
		self::$_logger[$file]->log($msg);
	}
}


class FileLog
{
	/**
	 * @var integer 单个日志文件的大小上限,默认:50M
	 */
	private $_maxFileSize=52428800; // in KB
	/**
	 * @var integer 保留的日志文件数量上限，默认：10个文件
	 */
	private $_maxLogFiles=10;
	/**
	 * @var string 日志文件的保存路径
	 */
	private $_logPath;
	/**
	 * @var string 日志的文件名
	 */
	private $_logFile='application.log';
	/**
	 * @var integer 一个请求缓存在内容中的日志数量 
	 */
	private $_autoFlush=10000;
	/**
	 * @var integer 当前的日志数量
	 */
	private $_logCount=0;
	/**
	 * @var boolean 逻辑上的写文件锁
	 */
	private $_processing=false;
	/**
	 * @var boolean 文件回滚清空的标记， 默认：false.
	 */
	public $rotateByCopy=false;
	/**
	 * @var array 所有的日志消息
	 */
	private $_logs = array();
	
	public function __destruct(){
			$this->processLogs();
			$this->_logs = array();
			$this->_logCount = 0;
	}
	
	/**
	 * 记录日志
	 * @param string $message
	 * @param string $level
	 * @param string $category
	 */
	public function log($message){
		$this->_logs[] = $message;
		$this->_logCount++;
		if($this->_autoFlush > 0 && $this->_logCount >= $this->_autoFlush && !$this->_processing)
		{
			$this->_processing = true;
			$this->processLogs();
			$this->_logs = array();
			$this->_logCount = 0;
			$this->_processing = false;
		}
	}
	
	/**
	 * @return string directory storing log files. Defaults to application runtime path.
	 */
	public function getLogPath()
	{
		return $this->_logPath;
	}

	/**
	 * @param string $value directory for storing log files.
	 * @throws CException if the path is invalid
	 */
	public function setLogPath($value)
	{
		$this->_logPath=realpath($value);
		if($this->_logPath===false || !is_dir($this->_logPath) || !is_writable($this->_logPath))
			throw new CException('日志目录："'.$this->_logPath.'"不存在或不可写');
	}

	/**
	 * @return string log file name. Defaults to 'application.log'.
	 */
	public function getLogFile()
	{
		return $this->_logFile;
	}

	/**
	 * @param string $value log file name
	 */
	public function setLogFile($value)
	{
		$this->_logFile=$value;
	}

	/**
	 * @return integer maximum log file size in kilo-bytes (KB). Defaults to 1024 (1MB).
	 */
	public function getMaxFileSize()
	{
		return $this->_maxFileSize;
	}

	/**
	 * @param integer $value maximum log file size in kilo-bytes (KB).
	 */
	public function setMaxFileSize($value)
	{
		if(($this->_maxFileSize=(int)$value)<1)
			$this->_maxFileSize=1;
	}

	/**
	 * @return integer number of files used for rotation. Defaults to 5.
	 */
	public function getMaxLogFiles()
	{
		return $this->_maxLogFiles;
	}

	/**
	 * @param integer $value number of files used for rotation.
	 */
	public function setMaxLogFiles($value)
	{
		if(($this->_maxLogFiles=(int)$value)<1)
			$this->_maxLogFiles=1;
	}

	/**
	 * Saves log messages in files.
	 * @param array $logs list of log messages
	 */
	private function processLogs()
	{
		$text=implode("\n",$this->_logs)."\n";
		$logFile=$this->getLogPath().DIRECTORY_SEPARATOR.$this->getLogFile();
		$fp=@fopen($logFile,'a');
		@flock($fp,LOCK_EX);
		if(@filesize($logFile)>$this->getMaxFileSize()*1024)
		{
			$this->rotateFiles();
			@flock($fp,LOCK_UN);
			@fclose($fp);
			@file_put_contents($logFile,$text,FILE_APPEND|LOCK_EX);
		}
		else
		{
			@fwrite($fp,$text);
			@flock($fp,LOCK_UN);
			@fclose($fp);
		}
	}

	/**
	 * Rotates log files.
	 */
	protected function rotateFiles()
	{
		$file=$this->getLogPath().DIRECTORY_SEPARATOR.$this->getLogFile();
		$max=$this->getMaxLogFiles();
		for($i=$max;$i>0;--$i)
		{
			$rotateFile=$file.'.'.$i;
			if(is_file($rotateFile))
			{
				// suppress errors because it's possible multiple processes enter into this section
				if($i===$max)
					@unlink($rotateFile);
				else
					@rename($rotateFile,$file.'.'.($i+1));
			}
		}
		if(is_file($file))
		{
			// suppress errors because it's possible multiple processes enter into this section
			if($this->rotateByCopy)
			{
				@copy($file,$file.'.1');
				if($fp=@fopen($file,'a'))
				{
					@ftruncate($fp,0);
					@fclose($fp);
				}
			}
			else
				@rename($file,$file.'.1');
		}
	}
}
