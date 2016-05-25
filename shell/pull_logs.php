<?php


class pull_logs
{
	var $m_arrServer;

	public function __construct()
	{
		$this->_Init();
	}
	public function __destruct()
	{
	}


	public function PrintShell()
	{
	}


	////////////////////////////////////////////////////////////////////////////////
	//	Private
	//

	private function _Init()
	{
		$this->m_arrServer	= $this->_GetServerList();
	}

	private function _GetServerList()
	{
		return
		[
			'101.200.161.72'	=>
				[
					'user.xs.cn',
				],
			'101.200.161.46'	=>
				[
					'account.xs.cn',
					'api-account.xs.cn',
				],
			'101.200.190.214'	=>
				[
					'data.xs.cn',
					'client.xs.cn',
					'magapi.data.xs.cn',
				],
			'101.200.164.86'	=>
				[
					'pay.xs.cn',
					'magapi.pay.xs.cn',
				],
			'101.200.161.104'	=>
				[
					'image.xs.cn',
				],
			'101.200.190.149'	=>
				[
					'www.xs.cn',
					'rd.xs.cn',
					'comment.xs.cn',
					'manage.xs.cn',
					'mailsender.service.xs.cn',
					'msgsender.service.xs.cn',
					'update.service.xs.cn',
				]
		];
	}


}