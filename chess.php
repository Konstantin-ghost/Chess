<?
require_once('chess_class.php');
use ChessBoard\ChessClass;

$chess_board = new ChessClass();

$chess_board->add_callback('all', 'fwrite(STDOUT, "figure added\n");');
$chess_board->add_callback('r', 'fwrite(STDOUT, "rook figure added\n");');


$chess_board->send_message("Welcome to chess, type help for command list");

while(TRUE)
{
	fwrite(STDOUT, "Command: ");
	$command_raw = chop(fgets(STDIN)); 

	$command = explode(" ", $command_raw);


	switch ($command[0]) 
	{
		case 'help':
			$chess_board->send_message("help                           This help
show                           show the current state of the chessboard
exit                           close application
add <x> <y> <type>             add the figure <type> to position x,y on the board
move <x> <y> <x_new> <y_new>   move the figure from cell x, y to cell x_new, y_new
remove <x> <y>                 remove a figure from a cell x, y
save <type> <id>               save the current state of the board, type can be redis or file
load <type> <id>               load board status, type can be redis or file");
			break;
		case "exit":
			exit(0);
			break;
		case 'show':
			$chess_board->show();
			break;
		case 'add':
			if(count($command) == 4)
			{
				$chess_board->add($command[1], $command[2], $command[3]);
			}
			else
			{
				$chess_board->send_message("not all arguments are specified");
			}
			break;
		case 'move':
			if(count($command) == 5)
			{
				$chess_board->move($command[1], $command[2], $command[3], $command[4]);
			}
			else
			{
				$chess_board->send_message("not all arguments are specified");
			}
			break;
		case 'remove':
			if(count($command) == 3)
			{
				$chess_board->move($command[1], $command[2]);
			}
			else
			{
				$chess_board->send_message("not all arguments are specified");
			}
			break;
		case 'save':
			if(count($command) == 3)
			{
				$chess_board->save($command[1], $command[2]);
			}
			else
			{
				$chess_board->send_message("not all arguments are specified");
			}
			break;
		case 'load':
			if(count($command) == 3)
			{
				$chess_board->load($command[1], $command[2]);
			}
			else
			{
				$chess_board->send_message("not all arguments are specified");
			}
			break;
		default:
			break;
	}
}