<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Comment;
use App\Models\Contact;
use App\Models\Coupon;
use App\Models\Item;
use App\Models\Payment;
use App\Models\Review;
use App\Models\Subscriber;
use App\Models\User as ModelsUser;
use App\Models\Wishlist;
use App\Models\Workshop;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Mail;

class User extends Controller
{
    public function userupdate(Request $request)
    {
        $rules =array(

            "token" => "required"
        );
        $validator= Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $validator->errors();
        } else {
            $user= ModelsUser::where('remember_token', $request->token)->first();
            if ($user) {
                $user = ModelsUser::where('remember_token', $request->token)->first();
                if(isset($request->first_name))
                $user->first_name = $request->first_name;
                if(isset($request->last_name))
                $user->last_name = $request->last_name;
                if(isset($request->city))
                $user->city = $request->city;
                if(isset($request->school))
                $user->school = $request->school;
                if(isset($request->grade))
                $user->grade= $request->grade;
                if(isset($request->about))
                $user->about= $request->about;
                if ($request->hasFile('icon')) {
                    $file = $request->file('icon')->store('public/profile/icon');
                    $user->icon  = $file;
                }
                if ($request->hasFile('banner')) {
                    $file = $request->file('banner')->store('public/profile/banner');
                    $user->banner  = $file;
                }
                $user->save();
                return response(["status" => true,"message" => "User updated sucessfully."], 200);  
            } else {
                return response(["status" => false,"message" => "Invalid token."], 200);
            }
        }
    }
    public function insertCart(Request $request)
{
    $rules = [
        'user_id' => 'required|integer',
        'is_bought' => 'required|boolean',
        'coupon_code' => 'nullable|max:255',
        'discount' => 'required|numeric',
        'price' => 'required|numeric',
        'payment_id' => 'nullable|max:255',
        'payment_status' => 'required|integer',
        'requesting_payment' => 'nullable|max:255',
        'order_id' => 'nullable|max:255',
        'verify_token' => 'nullable|max:255',
        'url' => 'nullable|max:255',
        'webhook' => 'required|boolean',
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return response($validator->errors(), 400);
    }

    try {
        $cart = new Cart();
        $cart->user_id = $request->user_id;
        $cart->is_bought = $request->is_bought;
        $cart->coupon_code = $request->coupon_code;
        $cart->discount = $request->discount;
        $cart->price = $request->price;
        $cart->payment_id = $request->payment_id;
        $cart->payment_status = $request->payment_status;
        $cart->requesting_payment = $request->requesting_payment;
        $cart->order_id = $request->order_id;
        $cart->verify_token = $request->verify_token;
        $cart->url = $request->url;
        $cart->webhook = $request->webhook;
        $cart->save();

        return response([
            'status' => true,
            'message' => 'Cart created successfully.',
            'data' => $cart
        ], 201);
    } catch (\Exception $e) {
        return response([
            'status' => false,
            'message' => 'Failed to insert cart.',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function insertComment(Request $request)
{
    $rules = [
        'user_id' => 'required|integer',
        'blog_id' => 'required|integer',
        'comment' => 'required|max:1000',
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return response($validator->errors(), 400);
    }

    try {
        $comment = new Comment();
        $comment->user_id = $request->user_id;
        $comment->blog_id = $request->blog_id;
        $comment->comment = $request->comment;
        $comment->save();

        return response([
            'status' => true,
            'message' => 'Comment created successfully.',
            'data' => $comment
        ], 201);
    } catch (\Exception $e) {
        return response([
            'status' => false,
            'message' => 'Failed to insert comment.',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function insertContact(Request $request)
{
    $rules = [
        'name' => 'required|max:255',
        'mobile' => 'required|max:255',
        'email' => 'required|email|max:255',
        'subject' => 'required|max:255',
        'query' => 'required|max:255',
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return response($validator->errors(), 400);
    }

    try {
        $contact = new Contact();
        $contact->name = $request->get('name');
        $contact->mobile = $request->get('mobile');
        $contact->email = $request->get('email');
        $contact->subject = $request->get('subject');
        $contact->query = $request->get('query');
        $contact->save();

        return response([
            'status' => true,
            'message' => 'Contact created successfully.',
            'data' => $contact
        ], 201);
    } catch (\Exception $e) {
        return response([
            'status' => false,
            'message' => 'Failed to insert contact.',
            'error' => $e->getMessage()
        ], 500);
    }
}


public function insertReview(Request $request)
{
    $rules = [
        'user_id' => 'required|integer',
        'trainer_id' => 'required|integer',
        'workshop_id' => 'required|integer',
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'required|max:1000',
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return response($validator->errors(), 400);
    }

    try {
        if(Review::where('user_id',$request->user_id)->where('workshop_id',$request->workshop_id)->first()){
            $review = Review::where('user_id',$request->user_id)->where('workshop_id',$request->workshop_id)->first();
        }else{
            $review = new Review();
        }
        
        $review->user_id = $request->user_id;
        $review->trainer_id = $request->trainer_id;
        $review->workshop_id = $request->workshop_id;
        $review->rating = $request->rating;
        $review->comment = $request->comment;
        $review->save();

        return response([
            'status' => true,
            'message' => 'Review created successfully.',
            'data' => $review
        ], 201);
    } catch (\Exception $e) {
        return response([
            'status' => false,
            'message' => 'Failed to insert review.',
            'error' => $e->getMessage()
        ], 500);
    }
}
public function insertSubscriber(Request $request)
{
    $rules = [
        'email' => 'required|email|max:255',
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return response($validator->errors(), 400);
    }

    try {
        $subscriber = new Subscriber();
        $subscriber->email = $request->email;
        $subscriber->save();

        return response([
            'status' => true,
            'message' => 'Email inserted successfully.',
            'data' => $subscriber
        ], 201);
    } catch (\Exception $e) {
        return response([
            'status' => false,
            'message' => 'Failed to insert subscriber.',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function insertWishlist(Request $request)
{
    $rules = [
        'user_id' => 'required|integer|exists:users,id',
        'workshop_id' => 'required|integer|exists:workshops,id',
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return response($validator->errors(), 400);
    }
    if(!ModelsUser::where('remember_token',$request->token)->where('user_type','user')->first()){
        return response(["status" =>"false", "message"=>"Session is expired. Please Login Again"], 401);
    }
    try {
        if(isset($request->del)){
            if($request->del==1){
                Wishlist::where('user_id',$request->user_id)->where('workshop_id',$request->workshop_id)->first()->delete();
                return response([
                    'status' => true,
                    'message' => 'Wishlist item deleted successfully.',
                    
                ], 201);
            }
        }
        
        if(Wishlist::where('user_id',$request->user_id)->where('workshop_id',$request->workshop_id)->first()){
            $wishlist = Wishlist::where('user_id',$request->user_id)->where('workshop_id',$request->workshop_id)->first();
        }else{
            $wishlist = new Wishlist();
        }
        $wishlist->user_id = $request->user_id;
        $wishlist->workshop_id = $request->workshop_id;
        $wishlist->save();

        return response([
            'status' => true,
            'message' => 'Wishlist item added successfully.',
            'data' => $wishlist
        ], 201);
    } catch (\Exception $e) {
        return response([
            'status' => false,
            'message' => 'Failed to insert wishlist item.',
            'error' => $e->getMessage()
        ], 500);
    }
}
public function addItemCart(Request $request)
{
    $rules = array(
        "token" => "required",
        "cart_id" => "required",
        "workshop_id" => "required"
    );
    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
        return $validator->errors();
    } else {
        $user = ModelsUser::where('remember_token', $request->token)->first();
        if ($user) {
            if(Payment::where('user_id', $user->id)->where('workshop_id', $request->workshop_id)->where('payment_status', 1)->first()){
                return response(["status" => false,"message" => "Hey ".$user->first_name." You have already purchased this workshop.", "m" => "purchased"], 200);
            }
            if(!Item::where('cart_id', $request->cart_id)->where('workshop_id', $request->workshop_id)->first()) {
                $workshop = Workshop::where('id', $request->workshop_id)->first();
                if($workshop->is_completed == 0) {
                    $price = $workshop->price;
                } else {
                    $price = $workshop->record_price;
                }
                $cart = Cart::find($request->cart_id);
                if($cart->coupon_code==NULL){
                    $feedback = new Item();
                    $feedback->cart_id = $request->cart_id;
                    $feedback->workshop_id = $request->workshop_id;
                    $feedback->price = $price;
                    $feedback->save();
                    $cart->price = $cart->price + $price;
                    $cart->save();
                }else{
                    $coupon = Coupon::where('coupon_code',$cart->coupon_code)->first();
                    $coupon_code = $coupon->coupon_code;
                    $discount_percent = (($coupon->value)/100);
                    $feedback = new Item();
                    $feedback->cart_id = $request->cart_id;
                    $feedback->workshop_id = $request->workshop_id;
                    $feedback->price = $price;
                    $feedback->discount = $price*$discount_percent;
                    $feedback->coupon_code = $coupon_code;
                    $feedback->save();
                    $cart->price = $cart->price + $price - $feedback->discount;
                    $cart->discount = $cart->discount + $feedback->discount;
                    $cart->save();
                }
                
                return response(["status" => true,"message" => $user->first_name." your workshop is Added in cart."], 200);
            } else {
                return response(["status" => false,"message" => "Hey ".$user->first_name." you have this workshop already in cart.", "m" => "in_cart"], 200);
            }
        } else {
            return response(["status" => false,"message" => "Invalid token."], 401);
        }
    }
}
public function addItemCartCoupon(Request $request)
{
    $rules = array(
        "token" => "required",
        "cart_id" => "required",
        "coupon_code" => "required",
    );
    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
        return $validator->errors();
    } else {
        $user = ModelsUser::where('remember_token', $request->token)->first();
        if ($user) {
            if(Cart::where('id', $request->cart_id)->where('coupon_code', NULL)->first()) {
                
                if(Coupon::where('coupon_code', $request->coupon_code)->where('count', '>',0)->first()){
                    $discount = Coupon::where('coupon_code', $request->coupon_code)->where('count', '>',0)->first();
                    $discount->count = $discount->count-1;
                    $discount->save();
                    $coupon_code = $discount->coupon_code;
                    $discount_percent = (($discount->value)/100);
                }else{
                    return response(["status"=> false, "message" => 'Invaild Coupon Code'],301);
                }
                $discount_amount = 0;
                $items = Item::where('cart_id', $request->cart_id)->get();
                foreach($items as $item){
                    $item->coupon_code =  $coupon_code;
                    $discount_amount = $discount_amount + $discount_percent*$item->price;
                    $item->discount = $discount_percent*$item->price;
                    $item->save();
                }
                $cart = Cart::find($request->cart_id);
                $cart->price = $cart->price - $discount_amount;
                $cart->discount = $cart->discount + $discount_amount;
                $cart->coupon_code = $coupon_code;
                $cart->save();
                return response(["status" => true,"message" => $user->first_name." you coupon code is applied. You have Saved Rs. $discount_amount."], 200);
            } else {
                $items = Item::where('cart_id', $request->cart_id)->get();
                $cart_price = 0;
                foreach($items as $item){
                    $item->coupon_code =  NULL;
                    $item->discount = NULL;
                    $cart_price = $cart_price + $item->price;
                    $item->save();
                }
                $cart = Cart::find($request->cart_id);
                $cart->price = $cart_price;
                $cart->discount = 0.00;
                $cart->save();

                if(Coupon::where('coupon_code', $request->coupon_code)->where('count', '>',0)->first()){
                    $discount = Coupon::where('coupon_code', $request->coupon_code)->where('count', '>',0)->first();
                    $discount->count = $discount->count-1;
                    $discount->save();
                    $coupon_code = $discount->coupon_code;
                    $discount_percent = (($discount->value)/100);
                }else{
                    return response(["status"=> false, "message" => 'Invaild Coupon Code'],301);
                }
                $discount_amount = 0;
                $items = Item::where('cart_id', $request->cart_id)->get();
                foreach($items as $item){
                    $item->coupon_code =  $coupon_code;
                    $discount_amount = $discount_amount + $discount_percent*$item->price;
                    $item->discount = $discount_percent*$item->price;
                    $item->save();
                }
                $cart = Cart::find($request->cart_id);
                $cart->price = $cart->price - $discount_amount;
                $cart->discount = $discount_amount;
                $cart->coupon_code = $coupon_code;
                $cart->save();
                return response(["status" => true,"message" => "Coupon Code Applied."], 200);
            }
        } else {
            return response(["status" => false,"message" => "Invalid token."], 401);
        }
    }
}
public function removeItemCart(Request $request)
{
    $rules = array(
        "token" => "required",
        "cart_id" => "required",
        "workshop_id" => "required"
    );
    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
        return $validator->errors();
    } else {
        $user = ModelsUser::where('remember_token', $request->token)->first();
        if ($user) {
            if(Item::where('cart_id', $request->cart_id)->where('workshop_id', $request->workshop_id)->first()) {
                $feedback = Item::where('cart_id', $request->cart_id)->where('workshop_id', $request->workshop_id)->first();
                if($feedback->coupon_code != NULL){
                        $code = Coupon::where('coupon_code', $feedback->coupon_code)->first();
                        $code->count = $code->count+1;
                        $code->save();
                }
                if($feedback->delete()){
                    $items = Item::where('cart_id', $request->cart_id)->get();
                $cart_price = 0;
                foreach($items as $item){
                    $item->coupon_code =  NULL;
                    $item->discount = NULL;
                    $cart_price = $cart_price + $item->price;
                    $item->save();
                }
                $cart = Cart::find($request->cart_id);
                $cart->price = $cart_price;
                $cart->discount = 0.00;
                $cart->save();
                return response(["status" => true,"message" => "Workshop Removed from cart."], 200);
                }

                
            } else {
                return response(["status" => true,"message" => "Workshop Not in cart."], 200);
            }
        } else {
            return response(["status" => false,"message" => "Invalid token."], 401);
        }
    }
}
public function cartPaymentInitiate(Request $request)
{
    $rules = array(
        "token" => "required",
        "cart_id" => "required",
        "amount" => "required"
    );
    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
        return $validator->errors();
    } else {
        if(ModelsUser::where('remember_token', $request->token)->first()){
        $user = ModelsUser::where('remember_token', $request->token)->first();
        if(Cart::where('user_id', $user->id)->where('payment_status', 1)->where('id', $request->cart_id)->first()) {
            return response(["status" => false,"message" => "Already Cart workshops are already bought."], 200);
        }
        if ($user) {
            $user = ModelsUser::where('remember_token', $request->token)->first();
            $payment = Cart::find($request->cart_id);
            $payment->requesting_payment = $request->amount;
            $payment->order_id = $request->order_id;
            $payment->verify_token = $request->verify_token;
            $payment->url = $request->url;
            $payment->save();
            return response(["status" => true,"message" => "User Transaction Initiated."], 200);
        } else {
            return response(["status" => false,"message" => "User Transaction Not Initiated."], 200);
        }
      }else{
        return response(["status" => true,"message" => "Login again | Session is expired."], 200);
      }
    }
}
public function cartPaymentFree(Request $request)
    {
        $rules = array(
            "token" => "required",
            "cart_id" => "required",
            "amount" => "required"
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $validator->errors();
        } else {
            if(ModelsUser::where('remember_token', $request->token)->first()){
            $user = ModelsUser::where('remember_token', $request->token)->first();
            if(Cart::where('user_id', $user->id)->where('payment_status', 1)->where('id', $request->cart_id)->first()) {
                return response(["status" => false,"message" => "Already Cart workshops are already bought."], 200);
            }
            if ($user) {
                $user = ModelsUser::where('remember_token', $request->token)->first();
                $cart = Cart::find($request->cart_id);
                $cart->requesting_payment = $request->amount;
                $cart->order_id = $request->order_id;
                $cart->verify_token = $request->verify_token;
                $cart->url = $request->url;
                $cart->payment_id = $request->payment_id;
                $cart->payment_status = 1;
                $cart->is_bought = 1;
                $cart->save();
                if ($cart) {
                    $user = ModelsUser::where('id', $cart->user_id)->first();
                    $items = Item::where('cart_id', $cart->id)->get();
                    $temp = 1;
                    foreach ($items as $item) {
                        $temp = $temp+1;
                        $payment = new Payment();
                        
                        $payment->workshop_id = $item->workshop_id;
                        $payment->user_id = $cart->user_id;
                        $payment->payment_id = $request->payment_id;
                        $payment->order_id = $cart->order_id.$temp;
                        $payment->verify_token = $cart->verify_token;
                        $payment->amount = $item->price - $item->discount;
                        $payment->payment_status = 1;
                        $payment->coupon_code = $item->coupon_code;
                        $payment->save();

                        $workshop = Workshop::where('id', $payment->workshop_id)->first();
                            $data = array(
                            "name" => $user->first_name,
                            "workshop_name" => $workshop->name,
                            "id" => $workshop->id,
                            "city" => $user->city
                        );
                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://app.notifyverse.in/pixel-webhook/53ae2cf1dfcf2f4bbdf1226db79c6615',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => $data,
                        ));
                        $response = curl_exec($curl);
                        curl_close($curl);
                    }
                    
                    try {
                        $data = ['amount' => $cart->price, 'id' => $cart->order_id, 'payment_id' => $request->payment_id ];
                        $email = $user->email;
                        $name = $user->first_name;
                        Mail::send('purchase', $data, function ($message) use ($email, $name) {
                            $message->to($email, $name)->subject('Congratulation Workshop Activated! | Free Workshop | Magic Of Skills');
                        });
                        
                    } catch (Exception $e) {
                        echo 'Caught exception: ',  $e->getMessage(), "\n";
                    }
                    
                    
                    
                    $cart = new Cart();
                            $cart->user_id = $user->id;
                            $cart->save();
                
                    return response(["status" => true,"message" => "Transaction is sucessfully completed. Workshops are added in your account.", "cart"=> $cart->id], 200);
                }

                return response(["status" => true,"message" => "User Transaction Initiated."], 200);
            } else {
                return response(["status" => false,"message" => "User Transaction Not Initiated."], 200);
            }
          }else{
            return response(["status" => true,"message" => "Login again | Session is expired."], 200);
          }
        }
    }
public function cartPaymentSucess(Request $request)
{
    $rules = array(
        "token" => "required",
        "order_id" => "required",
        "payment_id" => "required"
    );
    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
        return $validator->errors();
    } else {
        $cart = Cart::where('order_id', $request->order_id)->where('verify_token', $request->token)->where('payment_status', 0)->first();
       
        if ($cart) {
            $user = ModelsUser::where('id', $cart->user_id)->first();
            $cart->payment_status = 1;
            $cart->is_bought = 1;
            $cart->payment_id = $request->payment_id;
            $cart->save();
            $items = Item::where('cart_id', $cart->id)->get();
            $temp = 1;
            foreach ($items as $item) {
                $temp = $temp+1;
                $payment = new Payment();
                $payment->workshop_id = $item->workshop_id;
                $payment->user_id = $cart->user_id;
                $payment->payment_id = $request->payment_id;
                $payment->order_id = $cart->order_id.$temp;
                $payment->verify_token = $cart->verify_token;
                $payment->amount = $item->price - $item->discount;
                $payment->payment_status = 1;
                $payment->cpd = 1;
                $payment->coupon_code = $item->coupon_code;
                $payment->save();
                $workshop = Workshop::where('id', $payment->workshop_id)->first();
                $data = array(
                "name" => $user->first_name,
                "workshop_name" => $workshop->name,
                "id" => $workshop->id,
                "city" => $user->city
            );
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://app.notifyverse.in/pixel-webhook/53ae2cf1dfcf2f4bbdf1226db79c6615',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            }

            try {
                $data = ['amount' => $cart->price, 'id' => $cart->order_id, 'payment_id' => $request->payment_id ];
                $email = $user->email;
                $name = $user->first_name;
                Mail::send('purchase', $data, function ($message) use ($email, $name) {
                    $message->to($email, $name)->subject('Congratulation Workshop Activated! | Purchase Invoice | Magic Of Skills');
                });
                
            } catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }
            
            
            
            $cart = new Cart();
                    $cart->user_id = $user->id;
                    $cart->save();

            
            return response(["status" => true,"message" => "Transaction is sucessfully completed. Workshops are added in your account.", "cart"=> $cart->id], 200);
        } else {
            $cart = Cart::where('order_id', $request->order_id)->where('verify_token', $request->token)->first();
            $user_id = $cart->user_id;
            if(Cart::where('user_id',$user_id)->where('payment_status',0)->first()){
                $cart = Cart::where('user_id',$user_id)->where('payment_status',0)->first();
            }else{
                $cart = new Cart();
                    $cart->user_id = $user_id;
                    $cart->save();
            }
            return response(["status" => false,"message" => "Workshop is already Added", "cart" => $cart->id], 200);
        }
    }
}
public function cartPaymentSucessWebhook(Request $request)
{
    $rules = array(
        "token" => "required",
        "order_id" => "required",
        "payment_id" => "required"
    );
    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
        return $validator->errors();
    } else {
        $cart = Cart::where('order_id', $request->order_id)->where('payment_status', 0)->first();
        if ($cart) {
            $user = ModelsUser::where('id', $cart->user_id)->first();
            $cart->payment_status = 1;
            $cart->is_bought = 1;
            $cart->payment_id = $request->payment_id;
            $cart->webhook = 1;
            $cart->save();
            $items = Item::where('cart_id', $cart->id)->get();
            $temp = 0;
            foreach ($items as $item) {
                $temp = $temp+1;
                $payment = new Payment();
                $payment->workshop_id = $item->workshop_id;
                $payment->user_id = $cart->user_id;
                $payment->payment_id = $request->payment_id;
                $payment->order_id = $cart->order_id.$temp;
                $payment->verify_token = $cart->verify_token;
                $payment->amount = $item->price - $item->discount;
                $payment->payment_status = 1;
                $payment->coupon_code = $item->coupon_code;
                $payment->save();
                $workshop = Workshop::where('id', $payment->workshop_id)->first();
                $data = array(
                "name" => $user->first_name,
                "workshop_name" => $workshop->name,
                "id" => $workshop->id,
                "city" => $user->city
            );
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://app.notifyverse.in/pixel-webhook/53ae2cf1dfcf2f4bbdf1226db79c6615',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            }
            try {
                $data = ['amount' => $cart->price, 'id' => $cart->order_id, 'payment_id' => $request->payment_id ];
                $email = $user->email;
                $name = $user->first_name;
                Mail::send('purchase', $data, function ($message) use ($email, $name) {
                    $message->to($email, $name)->subject('Congratulation Workshop Activated! | Purchase Invoice | Magic Of Skills');
                });
                
            } catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }
           
            return response(["status" => true,"message" => "Transaction is sucessfully completed. Workshops are added in your account."], 222);
        } else {
            return response(["status" => false,"message" => "Payment Failure."], 200);
        }
    }
}
}