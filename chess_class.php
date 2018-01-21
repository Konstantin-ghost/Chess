<?
namespace ChessBoard;
use Redis;

class ChessClass
{
	private $x = 8;
	private $y = 8;
	private $types = array('r', 't', 'p');
	private $chess_board = array();
	private $redis;
	private $callbacks = array();

	public function __construct()
	{
		$this->redis = new Redis();
		$this->redis->connect('127.0.0.1', 6379);

		for ($x = 1; $x <= $this->x; $x++) 
		{
			for ($y = 1; $y <= $this->y; $y++)
			{
				$this->chess_board[$x][$y] = '';
			} 
		}
	}

	public function show()
	{
		for ($x = 1; $x <= $this->x; $x++) 
		{
			for ($y = 1; $y <= $this->y; $y++)
			{
				fwrite(STDOUT, '"'.$this->chess_board[$x][$y].'" ');
			} 

			fwrite(STDOUT, "\n");
		}
	}

	public function add($x, $y, $type)
	{
		if(array_search($type, $this->types) !== FALSE)
		{
			if($this->chess_board[$x][$y] == '')
			{
				if(!empty($this->callbacks['all']))
				{
					foreach ($this->callbacks['all'] as $code) 
					{
						eval($code);
					}
				}
				if(!empty($this->callbacks[$type]))
				{
					foreach ($this->callbacks[$type] as $code) 
					{
						eval($code);
					}
				}

				$this->chess_board[$x][$y] = $type;
				$this->send_message("success!");
			}
			else
			{
				$this->send_message("the place is already taken");
			}
			
		}
		else
		{
			$this->send_message("type not support");
		}
	}

	public function move($x, $y, $x_new, $y_new)
	{
		if($this->chess_board[$x][$y] != '')
		{
			$this->chess_board[$x_new][$y_new] = $this->chess_board[$x][$y];
			$this->chess_board[$x][$y] = '';
			$this->send_message("success!");
		}
		else
		{
			$this->send_message("no figure in place");
		}
	}

	public function remove($x, $y)
	{
		if($this->chess_board[$x][$y] != '')
		{
			$this->chess_board[$x][$y] = '';
			$this->send_message("success!");
		}
		else
		{
			$this->send_message("the cell is already empty");
		}
	}

	public function save($type, $id)
	{
		switch ($type) 
		{
			case 'file':
				$result = file_put_contents($id, serialize($this->chess_board));
				break;
			case 'redis':
				$result = $this->redis->set('chess_board:'.$id, serialize($this->chess_board));
				break;
		}

		if($result !== FALSE)
		{
			$this->send_message("success!");
		}
		else
		{
			$this->send_message("can't save");
		}
	}

	public function load($type, $id)
	{
		switch ($type) 
		{
			case 'file':
				$result = file_get_contents($id);
				break;
			case 'redis':
				$result = $this->redis->get('chess_board:'.$id);
				break;
		}

		if($result === FALSE)
		{
			$this->send_message("this save does not exist");
		}
		else
		{
			$array = unserialize($result);
			$this->load_array($array);
		}
	}

	public function load_array($array)
	{
		if($array === FALSE)
		{
			fwrite(STDOUT, "incorrect data\n");
		}
		else
		{
			if(count($array) == $this->x)
			{
				for ($x = 1; $x <= $this->x; $x++)
				{
					if(count($array[$x]) != $this->y)
					{
						$this->send_message("incorrect data");
						return;
					}
				}

				for ($x = 1; $x <= $this->x; $x++) 
				{
					for ($y = 1; $y <= $this->y; $y++)
					{
						$this->chess_board[$x][$y] = $array[$x][$y];
					}
				}
				$this->send_message("success!");

			}
			else
			{
				$this->send_message("incorrect data");
			}
		}
	}

	public function add_callback($type, $code)
	{
		if(array_search($type, $this->types) !== FALSE || $type == 'all')
		{
			$this->callbacks[$type][] = $code;
		}
	}

	public function send_message($message)
	{
		fwrite(STDOUT, $message."\n");
	}


}