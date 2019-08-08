<?php
namespace app\index\controller;

use Db;
use think\Controller;
use GatewayClient\Gateway;
use app\index\model\User;
use app\index\model\Room;
use app\index\model\Role;
use app\index\model\Room_user as Ru;
use think\Request;
use think\facade\Session;

class Index extends Controller
{    

    public function index()
    {
        return 'holle';
    }
    public function initialize()
    {
        // 设置GatewayWorker服务的Register服务ip和端口
        Gateway::$registerAddress = '127.0.0.1:1238';

    }

    public function login(Request $req)
    {
        $client_id = $req->client_id;
        $username =$req->username;
        $type = User::where('username',$username)->find();
        if($type != null){
            $uid = $type['id'];                 
        }else{
            $uid = User::insertGetId(['username'=>$username]);
            // Gateway::bindUid($client_id, $uid);
        }

        return json([
            'type' => 1,
            "user_id" => $uid,
            "username"=> $username,
        ]);
        
    }
    public function hall(Request $req)
    {
        $client_id = $req->client_id;
        $username =$req->username;
        $uid =$req->user_id;
        $room = Room::order('id','desc')->select();
        Gateway::bindUid($client_id, $uid);
        Gateway::joinGroup($client_id, "dt");
        $message_data = json_encode([
            'type'=>'hall',
            'message'=> $username."登录游戏",
            "room"  => $room
        ]);
        Gateway::sendToGroup("dt", $message_data);
        

    }

    public function create_room(Request $req)
    {   
        $uid = $req->user_id;
        $username = $req->user_name;
        $client_id = $req->client_id;
        // trace($username, 'username');
        $room = new Room;
        $oldroom = Room::where('user_name',$username)->find();
        if($oldroom == null){
            $room->save([
            'user_name'  =>  $username,
            ]);
            $room_id = $room->id;
            $newroom = Room::get($room_id);
            $message_data = json_encode([
                'type'=>'c_room',
                'style' => 'new',
                'message'=> $username."创建了房间",
                "room"  =>  $newroom ,
            ]);
            $data = [
                ['role' => 'pingmin','room_id'=>$room_id, 'num' => '9'],
                ['role' => 'pingmin','room_id'=>$room_id, 'num' => '9'],
                ['role' => 'pingmin','room_id'=>$room_id, 'num' => '9'],
                ['role' => 'langren','room_id'=>$room_id, 'num' => '9'],
                ['role' => 'langren','room_id'=>$room_id, 'num' => '9'],
                ['role' => 'langren','room_id'=>$room_id, 'num' => '9'],
                ['role' => 'lieren','room_id'=>$room_id, 'num' => '9'],
                ['role' => 'nvwu','room_id'=>$room_id, 'num' => '9'],
                ['role' => 'yuyanjia','room_id'=>$room_id, 'num' => '9'],
                ['role' => 'pingmin','room_id'=>$room_id, 'num' => '12'],
                ['role' => 'langren','room_id'=>$room_id, 'num' => '12'],
                ['role' => 'shouwei','room_id'=>$room_id, 'num' => '12'],
            ];
            Db::name('role')->insertAll($data);
        }else{
            $room_id = $oldroom->id;
            $message_data = json_encode([
                'type'=>'c_room',
                'style' => 'old',
                'message'=> $username."又创建了房间",
                "room"  =>  $oldroom ,
            ]);
        }
     
        
        // trace($uid, 'trace uid');
        // echo "dasd";exit();
        // $room_id = $newroom['id'] ?? $oldroom['id'];
        Gateway::bindUid($client_id, 'fg');
        Gateway::sendToGroup("dt", $message_data);
        // Gateway::sendToGroup($room_id, $message_data);
          return json([
            // "user_id" => $uid,
            "room_id"  =>  $room_id,
        ]);

    }

    public function room(Request $req)
    {   
        trace($req->room_id,'room_id');
        $client_id = $req->client_id;
        $room_id = $req->room_id;
        $uid = $req->user_id;
        $username = $req->username;

        $room = Room::get($room_id);


        $seat_list = Role::where('seat','<>','')->where('room_id',$room_id)->select();
        // Gateway::joinGroup($client_id, "dt");
        $ru = new Ru;
        $ru->save([
            'room_id'  =>  $room_id,
            'user_name' => $username
        ]);
            Gateway::bindUid($client_id, $uid);
            $room = Room::get($room_id);
            $user = Ru::where('room_id',$room_id)->select();
             $message_data = json_encode([
                'type'=>'room',
                'message'=> $username."进入房间",
                "user"  => $user,
                "role"  => '0',
                'room' => $room,
                'seat_list'=>$seat_list
            ]);
        
        Gateway::joinGroup($client_id, $room_id);
        Gateway::sendToGroup($room_id, $message_data);

        $room = Room::get($room_id);
        $room->nums    = ['inc', 1];
        $room->force()->save();
        $room = Room::select();
        $message_data = json_encode([
            'type'=>'dt',
            'room'=> $room
        ]);
        Gateway::sendToGroup('dt', $message_data);
        
        $room = Room::get($room_id);
        if($room['nums'] == $room['limits']){
            $message_data = json_encode([
                'type'=>'start',
                'message'=> "人数到齐，可以开始游戏了",
            ]);
            Gateway::sendToGroup($room_id, $message_data);
            Gateway::sendToUid("fg", $message_data);
        }
        
    }

    public function setlimit(Request $req)
    {
        $room_id = $req->room_id;
        $limits = $req->limits;
        $room = Room::where('id',$room_id)->find();
        $room->limits = $limits;
        $room->save();

        $room_All = Room::order('id', 'desc')->select();
        $message_data = json_encode([
            'type'=>'dt',
            'room'=> $room_All
        ]);
        Gateway::sendToGroup('dt', $message_data);


        // 重置房间
        $message_data = json_encode([
            'type'=>'restart',
            'limits'=> $limits
        ]);
        Gateway::sendToGroup($room_id, $message_data);
    }



    public function game(Request $req)
    {
        // $client_id = $req->client_id;
        $room_id = $req->room_id;
        $uid = $req->user_id;
        $user_name = $req->user_name;

        // Gateway::bindUid($client_id, $uid);
        // Gateway::joinGroup($client_id, "yx");
        $room = Room::get($room_id);
        if($room['limits'] == 9){
            $role = Role::where('room_id',$room_id)->where('num',9)->where('user_id',null)->select();
         
                for($i=0;$i<count($role);$i++){
                   $roles[$i] = $role[$i]['role'];              
                }
                $a =array_rand($roles,1);
                $role2 = Role::where('id',$role[$a]['id'])->find();
                $role2->user_id = $uid;
                $role2->user_name = $user_name;
                $role2->save();
                return json([
                  "user_id" => $uid,
                  "user_name" => $user_name,
                  "role"  => $roles[$a],
                //   'ro' =>10
                ]);       
        }else{
            $role = Role::where('room_id',$room_id)->where('user_id',null)->select();
          
                for($i=0;$i<count($role);$i++){
                   $roles[$i] = $role[$i]['role'];              
                }
                $a =array_rand($roles,1);
                $role2 = Role::where('id',$role[$a]['id'])->find();
                $role2->user_id = $uid;
                $role2->user_name = $user_name;
                $role2->save();
                
                return json([
                    "user_id" => $uid,
                    "role"  => $roles[$a],
                    "user_name"  => $user_name,
                    //   'ro' =>count($role)
                ]);
        }
    }

    public function seat(Request $req){
        $seat = $req->anniu_umber;
        $user_id = $req->user_id;
        $user_name = $req->user_name;
        $room_id = $req->room_id;

        $role_1 = Role::where('room_id',$room_id)->where('user_id',$user_id)->find();
        if(!$role_1){
            $room = Room::get($room_id);
            $roleList = Role::where('room_id',$room_id)
                ->where('num',$room['limits'])
                ->where('user_id',null)
                ->select();
                
            
            for($i=0;$i<count($roleList);$i++){
                $roles[$i] = $roleList[$i]['role'];              
            }
            $a =array_rand($roles,1);
            $role2 = Role::where('id',$roleList[$a]['id'])->find();
            trace($user_id, 'user_id');
            $role2->user_id = $user_id;
            $role2->user_name = $user_name;
            $role2->save();
            
        }

        if(!$user_name){
            $role = Role::where('room_id',$room_id)->where('user_id',$user_id)->update(['seat'=> '']);
        }else{
            $role = Role::where('room_id',$room_id)->where('user_id',$user_id)->update(['seat'=>$seat]);
        }
            
        $role = Role::field('role')->where('room_id',$room_id)->where('user_id',$user_id)->find();
        trace($role, 'role');

        // $anniu_umber = Role::where('seat','<>',null)->select();
        
        $message_data = json_encode([
            'type'=>'seat',
            'anniu_umber'=> $seat,
            'user_name'=> $user_name,
            'user_id' => $user_id,
            'role' => $role['role'],
        ]);
        Gateway::sendToGroup($room_id, $message_data);
        Gateway::sendToUid("fg", $message_data);

        // 判断是否满员
        $limits = Room::get($room_id);
        $limits = $limits['limits'];
        if($limits == 9){
            $role2 = Role::where('room_id',$room_id)->where('num',9)->where('seat','')->select();
            if($role2 == null){
                $r9 = Role::select();
                $message_data = json_encode([
                    'type'=>'fg',
                    'message'=>'玩家已就位',
                    'role'=> $r9
                ]);
                Gateway::sendToUid("fg", $message_data);
            }
        }else{
            $role2 = Role::where('room_id',$room_id)->where('seat','')->select();
            if($role2 == null){
                $r12 = Role::select();
                $message_data = json_encode([
                    'type'=>'fg',
                    'message'=>'玩家已就位',
                    'role'=> $r12
                ]);
                Gateway::sendToUid("fg", $message_data);
            }
        }

        return json([
            'role' => $role['role'],
        ]);

    }


    // 法官
    public function over(Request $req){
        $room_id = $req->room_id;
        Role::where('room_id',$room_id)->delete(); 
        Room::where('id',$room_id)->delete(); 
        Ru::where('room_id',$room_id)->delete(); 
        $message_data = json_encode([
            'type'=>'over',
        ]);
        Gateway::sendToGroup($room_id, $message_data);
        $room_All = Room::order('id', 'desc')->select();
        $message_data = json_encode([
            'type'=>'dt',
            'room'=> $room_All
        ]);
        Gateway::sendToGroup('dt', $message_data);
    }

    public function restart(Request $req){
        $room_id = $req->room_id;
        $limits = $req->limits;
        
        Db::execute("update role set user_id=null,seat=null,user_name=null where room_id = {$room_id};"); 
        $message_data = json_encode([
            'type'=>'restart',
            'limits' => $limits
        ]);
        Gateway::sendToGroup($room_id, $message_data);
    }
    // 玩家
    public function close(Request $req){
        $room_id = $req->room_id;
        $user_id = $req->user_id;
        $user_name = $req->user_name;

        Role::where('user_id',$user_id)
            ->where('room_id', $room_id)
            ->update([
                'user_id' => null,
                'user_name' => null,
                'seat' => null,
            ]);
        $message_data = json_encode([
            'type'=>'close',
            'user_name'=> $user_name,
            'user_id' => $user_id,

        ]);
        Gateway::sendToGroup($room_id, $message_data);
        $num = Room::where('id',$room_id)->find();
        $num->nums = ['dec', 1];
        $num->save();
        $room_All = Room::order('id', 'desc')->select();
        $message_data = json_encode([
            'type'=>'dt',
            'room'=> $room_All
        ]);
        Gateway::sendToGroup('dt', $message_data);
    } 
}