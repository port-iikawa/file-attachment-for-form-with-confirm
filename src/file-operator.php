<?php
class FileOperator {
    private static ?object $instance = null;
    private string $tmp_dir = '';

    private function __construct() {
        //ファイルの一時格納を行うディレクトリを決定
        $this->tmp_dir = __DIR__ . '/tmp-files';
        $this->check_dir();
    }

    public function check_dir(): void {
        //ファイルの一時格納を行うディレクトリの存在確認をし、無ければ作成
        if (!is_dir($this->tmp_dir)) {
            mkdir($this->tmp_dir, 0700);
            file_put_contents("{$this->tmp_dir}/.htaccess", "deny from all");
        }
    }

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function upload_file(): array {
        //index.phpから送られてきた添付ファイルの情報を取得
        $file_info = $_FILES['attachment_file'];

        //ファイルの拡張子を取得
        preg_match("/\.[^.]+$/", $file_info['name'], $extension_match);
        $extension = $extension_match[0];

        //ファイルがサーバー内で予め決められた一時ディレクトリに正しくアップロードされているか確認
        if (!is_uploaded_file($file_info['tmp_name'])) {
            return false;
        }

        //一時ファイルの名称を作成
        //同時にフォームが利用された場合に、ユーザーAの添付ファイルをユーザーBの添付ファイルが上書きしないよう、ユニークなファイル名を付ける
        $now_time = str_replace('.', '', microtime(true));
        $rand = mt_rand();
        $file_name = $now_time . $rand . $extension;

        //サーバー内で予め決められた一時ディレクトリから、このプログラムで利用する一時ディレクトリにファイルを移動
        move_uploaded_file($file_info['tmp_name'], "{$this->tmp_dir}/{$file_name}");

        //アップロードされたファイル名と、一時ディレクトリに保存したファイル名をreturn
        return [
            'upload_file_name' => $file_info['name'],
            'tmp_file_name' => $file_name
        ];
    }

    public function get_file_path(string $tmp_file_name): string {
        return "{$this->tmp_dir}/{$tmp_file_name}";
    }

    public function del_file(string $tmp_file_name): void {
        unlink("{$this->tmp_dir}/{$tmp_file_name}");
    }

    public function garbage_collection(): void {
        //ファイル作成からどれくらいの時間が経過したら削除するのか、秒単位で指定
        $del_time_seconds = 10 * 60; //30分 * 60秒

        //一時ディレクトリに残っているファイル一覧を取得
        $tmp_files = glob("{$this->tmp_dir}/*");
        foreach($tmp_files as $file_path) {
            //ファイルを作成した時間
            $file_create_time = filemtime($file_path);

            //ファイルを作成してからの経過時間
            $elapsed_time_since_create = time() - $file_create_time;

            //ファイルを作成してからの経過時間が、予め決めておいた削除基準時間を超過していたら、削除
            if($elapsed_time_since_create > $del_time_seconds) {
                unlink($file_path);
            }
        }
    }
}