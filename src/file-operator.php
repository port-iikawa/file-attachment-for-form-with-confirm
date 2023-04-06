<?php
class FileOperator {
    private static ?object $instance = null;
    private string $tmp_dir = '';
    //許可するファイル拡張子と、MIMEタイプ
    private array $allowed_extensions = [
        '.pdf' => 'application/pdf',
        '.ppt' => 'application/vnd.ms-powerpoint',
        '.pptm' => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
        '.pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        '.xls' => 'application/vnd.ms-excel',
        '.xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        '.zip' => 'application/zip',
    ];
    private int $max_file_size_mb = 2;

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
        $return = ['result' => false, 'msg' => ''];

        //添付ファイルが送信されているか確認
        if (!isset($_FILES['attachment_file'])) {
            $return['msg'] = 'ファイルが送られていません';
            return $return;
        }

        //index.phpから送られてきた添付ファイルの情報を取得
        $file_info = $_FILES['attachment_file'];
        
        //ファイル名に拡張子が含まれているか確認
        preg_match("/\.[^.]+$/", $file_info['name'], $extension_match);

        if (empty($extension_match)) {
            $return['msg'] = '拡張子が含まれていません';
            return $return;
        }

        //拡張子が予め許可されたものに一致するか確認
        $extension = $extension_match[0];
        if (!isset($this->allowed_extensions[$extension])) {
            $return['msg'] = '許可されていない拡張子です';
            return $return;
        }

        //拡張子に対して、MIMEタイプが正しいものか確認
        if ($this->allowed_extensions[$extension] !== $file_info['type']) {
            $return['msg'] = 'MIMEタイプが不正です';
            return $return;
        }

        //予め定めたファイル容量内に収まっているか確認
        $max_file_size_kb = $this->max_file_size_mb * 1024 * 1024;
        if($file_info['size']  > $max_file_size_kb) {
            $return['msg'] = "アップロード出来るファイルサイズは{$this->max_file_size_mb}MBまでです。";
            return $return;
        }

        //ファイルがサーバー内で予め決められた一時ディレクトリに正しくアップロードされているか確認
        if (!is_uploaded_file($file_info['tmp_name'])) {
            $return['result'] = false;
            $return['msg'] = 'ファイルのアップロードに失敗しました';
            return $return;
        }

        //一時ファイルの名称を作成
        //同時にフォームが利用された場合に、ユーザーAの添付ファイルをユーザーBの添付ファイルが上書きしないよう、ユニークなファイル名を付ける
        $now_time = str_replace('.', '', microtime(true));
        $rand = mt_rand();
        $file_name = $now_time . $rand . $extension;

        //サーバー内で予め決められた一時ディレクトリから、このプログラムで利用する一時ディレクトリにファイルを移動出来たか確認
        if (!move_uploaded_file($file_info['tmp_name'], "{$this->tmp_dir}/{$file_name}")) {
            $return['msg'] = 'ファイルのアップロードに失敗しました';
            return $return;
        }

        //アップロードされたファイル名と、一時ディレクトリに保存したファイル名、実行結果をreturn
        return [
            'result' => true,
            'msg' => 'ファイルのアップロードに成功しました',
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