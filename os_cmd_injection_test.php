<?php

/**
 * 【悪用厳禁】
 * 表題：OSコマンドインジェクションテスト（PHP）
 * 説明：OSコマンドインジェクション脆弱性を検証するためのPHPプログラムです。
 * 概要：フォームからサブミットされたコマンドを受け取りOSコマンドを実行して結果を表示する。
 * 
 * ■ PHPの外部プログラムを実行できる主な関数
 * exec()     ：コマンド結果の最後の行を返します。
 * system()   ：成功時はコマンド出力の最後の行を返し、失敗時は false を返します。
 * passthru() ；値を返しません。一切干渉を受けずに直接コマンドから全てのデータを受けとる。
 * ※他にもPHPの外部プログラムを実行する関数は存在するため要確認のこと。
 * 
 * 今回は結果を全て見えるようにpassthru()を利用する。標準出力に書き出されるため溜めて変数に受け取る。
 * 
 * ■ OSコマンドインジェクション対策
 * 外部コマンドが実行できる関数を利用する際は下記のような事に注意する。
 * １．外部から渡された値を実行しない構造にする。
 * ２．外部からコマンドを渡して実行させる場合は、指定されたコマンド以外が実行されないようにする。
 * ３．さらに「;」による連続コマンド 「>」によるファイル書き出しも考慮する。
 * 　　特殊記号を排除する「;」「>」「<」「|」「&」「`」「(」「)」「$」「*」「?」「{」「}」「[」「]」「!」など。
 * ４．別の脆弱性により不正なファイルアップロードが行われた場合に
 * 　　OSコマンドインジェクションが混入する可能性があるため「改ざん検知」を検討する。
 * ５．プログラムを実行するユーザー（WEBサーバの実行ユーザー）の権限を最低限にする。
 */

// OSコマンド実行
$exec_cmd = "";
if ( isset($_POST['exec_cmd']) ) {
  $exec_cmd = $_POST['exec_cmd'];
  ob_start();
  passthru($exec_cmd);
  $cmd_result = ob_get_contents();
  ob_end_clean();
  $cmd_result = nl2br($cmd_result);
}

// 画面表示（HTML）
$html =<<<__EOL__
<!DOCTYPE html>
<html>
<head>
</head>
<body>
<h1>OS Command Injection Test Program</h1>
<div>---- Input Command To Submit----</div>
<div>
<form method="post">
<input type="text" name="exec_cmd"></input><br />
<input type="submit" value="submit"></input>
</form>
</div>
<br />
<div>---- Exec OS Cmd ----</div>
<div>
$exec_cmd<br />
</div>
<div>---- OS Cmd Result ----</div>
<div>
$cmd_result
</div>
</body>
</html>
__EOL__;

header("Content-type: text/html; charset=UTF-8");
echo $html;

?>
