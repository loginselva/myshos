<!DOCTYPE html>
<html>
 <head>

  <title>My Shops</title>
  <meta name="google-signin-client_id" content="784354227250-lg8q5didqc1ok2115sda26aml72r4utu.apps.googleusercontent.com"/>
  <link href="assets/style.css" type="text/css" rel="stylesheet" />
  <script type="text/javascript" src="assets/angular.min.js"></script>
  <script src="https://apis.google.com/js/platform.js" async defer></script>
  <script type="text/javascript">
  function onSignIn(googleUser) {
    var profile = googleUser.getBasicProfile();
    console.log('ID: ' + profile.getId()); // Do not send to your backend! Use an ID token instead.
    console.log('Name: ' + profile.getName());
    console.log('Image URL: ' + profile.getImageUrl());
    console.log('Email: ' + profile.getEmail());
  }

  function signOut() {
    var auth2 = gapi.auth2.getAuthInstance();
    auth2.signOut().then(function () {
      console.log('User signed out.');
    });
  }

  </script>
 </head>
 <body ng-app="app" ng-controller="list_cart">
  <div id="shopping-cart" >
    <div class="txt-heading" ng-show="is_visible">
      <input type="text" name="username"  ng-model="username" value="" placeholder="username">
      <input type="password" name="password" ng-model="password" value="" placeholder="password">
      <input type="submit" ng-click="loginprocess('login'); " name="login_btn" value="login" />
      <input type="submit" ng-click="loginprocess('register'); " name="register_btn" value="SignUp" />
      <span>{{err_message}}</span>
    </div>
    <div class="txt-heading" ng-init="isLogged();" ng-show="is_logged"> Welcome {{logged_user}}
        <a id="btnEmpty" href="javascript:void(0);" ng-click="logout();">Logout</a>
     </div>    
   <div class="txt-heading">Shopping Cart  
    <a id="btnEmpty" href="javascript:void(0);" ng-click="emptyCartlist();">Empty Cart</a> 
    <a id="btnEmpty" href="javascript:void(0);" style="background-color:grey;" ng-show="is_logged" ng-click="checkout();">Check Out</a>
  </div>

  <div class="g-signin2" data-onsuccess="onSignIn"></div>
  <a href="#" onclick="signOut();">Sign out</a>
  <table cellpadding="10" cellspacing="1">
   <tbody ng-init="getCartlist()">
   <tr>
    <th><strong>Name</strong></th>
    <th><strong>Code</strong></th>
	<th><strong>Quantity</strong></th>
	<th><strong>Price</strong></th>
	<th><strong>Action</strong></th>
   </tr>
   <tr ng-show="!mycartList.length" ng-repeat="mylist in mycartList  track by $index">
	<td><strong>{{mylist.name}}</strong></td>
	<td>{{mylist.code}}</td>
	<td>{{mylist.quantity}}</td>
	<td align=right>$ {{mylist.price * mylist.quantity}}</td>
	<td><a href="javascript:void(0);" data-code="{{mylist.code}}" ng-click="removeCartlist($event)" class="btnRemoveAction">Remove Item</a></td>
   </tr>
   </tbody>
  </table>		
 
  </div>

<div id="product-grid" ng-init="intialFun()">
	<div class="txt-heading">Products {{name}}</div>
		<div class="product-item" ng-repeat="cart in cartList">
			<div class="product-image"><img ng-src="{{cart.image}}" /></div>
			<div><strong>{{cart.name}}</strong></div>
			<div class="product-price">{{cart.price}}</div>
			<div>
				<input type="text" name="quantity" ng-model="quantity" value="1" size="2" />
				<input type="submit" value="Add to cart" data-code="{{cart.code}}" data-quantity="{{quantity}}" ng-click="addCartlist($event,quantity)" class="btnAddAction" />
			</div>
			
		</div>
</div>


<script type="text/javascript">
var app = angular.module("app",[]);

app.controller("list_cart",["$scope","$http",function($scope,$http){
	$scope.name = 'test';
	$scope.cartList = "";
	$scope.mycartList = "";
  $scope.is_logged = false;
  $scope.is_visible = true;
  $scope.err_message = '';
  
	$scope.getCartlist=function(){	
   		$http({
            method : 'POST',
            url : 'includes/sample_controller.php',
            data: {action:'getcart'},
            headers : {'Content-Type': 'application/x-www-form-urlencoded'}  
    	}).success(function(res){ 
              $scope.mycartList = res;         
    	});
   };
	$scope.intialFun= function(){ 
				$http({
		            method : 'POST',
		            url : 'includes/sample_controller.php',
		            data: {action:'getData'},
		            headers : {'Content-Type': 'application/x-www-form-urlencoded'}  
		    	}).success(function(res){
		            $scope.cartList = res;
		    	});
		};
   	$scope.addCartlist = function(e,qty){
   		var me = $(e.target);
   		var code = me.data('code');
   		var quantity = qty; 
   		$http({
   			method:'POST',
   			url:'includes/sample_controller.php',
   			data:{action:'add',code:code,quantity:quantity},
   			headers : {'Content-Type': 'application/x-www-form-urlencoded'}
   		}).success(function(res){
   			console.log(res);
   			$scope.getCartlist();
   		});
   };
   
   $scope.removeCartlist = function(e){ 
   		var me = $(e.target);
   		var code = me.data('code'); 
   		$http({
   			method:'POST',
   			url:'includes/sample_controller.php',
   			data:{action:'remove',code:code},
   			headers : {'Content-Type': 'application/x-www-form-urlencoded'}
   		}).success(function(res){ console.log(res);
   			$scope.getCartlist();
   		});
   };

   $scope.emptyCartlist = function(e){
   		$http({
   			method:'POST',
   			url:'includes/sample_controller.php',
   			data:{action:'empty'},
   			headers : {'Content-Type': 'application/x-www-form-urlencoded'}
   		}).success(function(res){
   			$scope.getCartlist();
   		});
   };  

   $scope.loginprocess = function(action){
      var username = $scope.username;
      var password = $scope.password;
      $http({
        method:'post',
        url:'includes/sample_controller.php',
        data:{action:action,username:username,password:password},
        headers : {'Content-Type': 'application/x-www-form-urlencoded'}
      }).success(function(res){
           console.log(res);
          if(angular.isObject(res)){
            $scope.logged_user = res.username;
            $scope.is_logged = true;
            $scope.is_visible = false;
            $scope.isLogged();
          }else{
            $scope.err_message = "Sorry user doesnot exist. ";
          }           
      });
   };

  
   $scope.isLogged = function(){
      $http({
        method:'post',
        url:'includes/sample_controller.php',
        data:{action:'loginsession'},
        headers : {'Content-Type': 'application/x-www-form-urlencoded'}
      }).success(function(res){
          //console.log(res);  
          if(angular.isObject(res)){
            $scope.logged_user = res.username;
            $scope.is_logged = true;
            $scope.is_visible = false;                    
          }

      });
   }

   $scope.logout = function(){
     $http({
        method:'post',
        url:'includes/sample_controller.php',
        data:{action:'logout'},
        headers : {'Content-Type': 'application/x-www-form-urlencoded'}
      }).success(function(res){
            $scope.is_logged = false;
            $scope.is_visible = true;                    
      });
   }

   $scope.checkout = function(){
      $http({
          method:'post',
          url:'includes/sample_controller.php',
          data:{action:'checkout'},
          headers : {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(res){ 
            alert("Thanks for shopping. You will be receiving your product shortly.");
             $scope.emptyCartlist();                   
        });
   }
	
}]);

</script>

</body>
</html>