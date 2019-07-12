# Laravel Shopping Cart by Ayeni Olanrewaju Joshua

#=============== Description ==========================

This Shopping cart package is meant to reduce or nullify the stress you'll go through to write a shoppin cart script
as you can extend it by implementing the interface. You can also listen to events at every phase of the cart application.

#---Post installation----------------
    After installation
    add AyeniJoshua\LaravelShoppingCart\Providers\CartServiceProvider::class to prividers array in your config/app.php file
    add 'Cart' => AyeniJoshua\LaravelShoppingCart\Facades\CartFacade::class to your alias array to use cart Facade
    run composer dump-autoload - for eazy package discovery

  #------------Package assets--------------
    The package contains (config,commands,events and listeners) assets
    to publish all assest
        run php artisan vendor:publish ayenijoshua/LaravelShoppingCart
    to publish only config (required)
        run php artisan vendor:publish ayenijoshua/LaravelShoppingCart --tag=config
    to publish only commands
        run php artisan vendor:publish ayenijoshua/LaravelShoppingCart --tag=commands
    to publish only events and listeners
        run php artisan vendor:publish ayenijoshua/LaravelShoppingCart --tag=events

#------This package relies on your laravel session and database configurations
    Whatever settings you set for your (Session,database) is what the cart is going to use. hence, if you can configure
    laravel session to use redis, the cart makes use of Redis (same is applicable to database).

# Available storages
1 - Session storage
        to use session storage, open ayenicart in your config directory and set storage to session
2 - Database storage
        to use database storage, open ayenicart in your config directory and set storage to database
3 - Multiple storage (enables you to store cart in both session and database)
        to use multiple storage, open ayenicart in your config directory and set multiple_storage.activate to true
        if you want to use default multiple storage, leave multiple_storage.default as true else change multiple_storage.default to false
    
#-------Facade Usage--------------

  #---------- set cart name--------
    Cart::setName('cart-name') -> use only when using session storage

  #-------add to cart----------
    Cart::add($id,$price,[$options]) 
    $id - Unique id of the item
    $price - price of the item
    $options - additional properties of item. (not required)

  #----------update cart--------
    Cart::update($id,$qty,[$options])
    $id - Unique id of the item
    $qty - quantity of the item
    $options - additional properties of item. (not required)

  #----------get an item--------------
    Cart::get($id)
    $id - Unique id of the item
    return item array - Array ( [weep] => Array ( [qty] => 3 [price] => 70 [totalPrice] => 210 [options] => Array ( ) ) )

  #----------Remove an item------------
    Cart::remove($id)
    $id - Unique id of the item

  #----------------Empty the cart--------
    Cart::empty()

  #--------------Destroy the cart-------
    Cart::destroy()

  #--------------Get all items from the cart--------
    Cart::all()
    returns array of items - Array ( [weep] => Array ( [qty] => 3 [price] => 70 [totalPrice] => 210 [options] => Array ( ) ) )

  #-----------------Restore cart---------
    Cart::restore($cart)
    $cart - cart object

  #------------------Get cart options---------
     Cart::getOptions($id)
     $id - Unique id of cart item  
     returns an array ( [options] => Array ( ) )

  #---------------Get storage the cart is using (returns database or session)-------
    Cart::getStorage()

  #--------------------Set cart name -----------------
     Cart::setName($name)
     $name - name to set for cart

  #------------------------Get cart name (gets name of current cart)---------------
      Cart::getName()

  #--------------------Get cart manager instance (CartDefaultSessionStorage, CartDefaultDatabaseStorage)-----------------------
      Cart::getCart($name)
      $name - cart name
      e.g $cart - $this->cart->getCart('whishlist')->add(params);

 #--------------------Get cart instance not recommended (Cart)-------------
     Cart::getCart();
     Note - You will not have access to the storage manager's methods.
     Methods avaailable are - (setName(),addToCart,updateCart,emptyCart and destroyCart)

#---------------Get total price-----------------
    Cart::totalPrice()

#--------------Get total quantity--------------
    Cart::totalQuantity()

#---------------------------------------Dependency injection usage---------

   #-------Using session storage----------
     #---------------Get storage the CartDeafultSessionStorage instance-------
        Cart::getStorage($cart_name)

        if you want to use only session storage

        Class ProductController extends Controller{

            function __construct(CartDeafultSessionStorage $cart){
        
                $this->cart = $cart;
            }

            function addProduct(){
                $cart = $this->cart->setName('product-cart');
                $cart->add(1,200);
                $cart->getName();
                $cart->update()
                $cartManagerInstance = $this->getCart('product-cart'); //returns CartDeafultSessionStorage object related to product-cart,
                $cartOjectInstance = $this->getCart(); //returns Cart object (a service used by every cart inplementation)
            }
        }

  #-------Using database storage----------
    #---------------Get storage the CartDeafultDatabseStorage instance-------
        Cart::getStorage($cart_name)

    #----------------------Set storage (Sets cart name and data in database----------
        Cart::setStorage($storage_type,$name)
        $name - name of cart

        if you want to use only databse storage

        Class ProductController extends Controller{

            function __construct(CartDeafultDatabseStorage $cart){
        
                $this->cart = $cart;
            }

            function addProduct(){
                $cart = $this->cart->setName('product-cart');
                $cart->add(1,200);
            }
        }

  #-------Using multiple storage----------
     #---------------Get storage the cart CartStorageInterface instance (this resolves to CartDefaultMultipleStorage class)-------
        Cart::getStorage($cart_name)

     #----------------------Set storage (Sets cart name and data in database----------
        Cart::setStorage($storage_type,$name)
        $name - name of cart

        if you want to use both session and database storage

        Class ProductController extends Controller{

            function __construct(CartStorageInterface $cart){
        
                $this->cart = $cart;
            }

            function addProduct(){
                #this adds cart items to both session and database

                $cart = $this->cart->setStorage('db','product-cart'); sets cart's storage to database and name to product-cart
                $cart->add(1,200);

                $cart = $this->cart->setStorage('session','product-cart'); sets cart's storage to session and name to product-cart
                $cart->add(1,200);

                #note - if no storage is set, cart defaults to session
                $cart = $this->cart->setName('product-cart')
            }
        }

  #-------Fluent interfacing----------
        Class ProductController extends Controller{

            function __construct(CartStorageInterface $cart){
        
                $this->cart = $cart;
            }

            function addProduct(){
                
                $cart = $this->cart->setStorage('db','product-cart')->add(1,200); 
                The above sets cart's storage to database and name to product-cart, adds item to cart and gets the cart name

                $cart = $this->cart->setStorage('session','product-cart')->add(1,200)->getName(); 
                The above sets cart's storage to session and name to product-cart and gets the cart name

                $cart->getCart('product-cart')->getStorage();
                the above get an instance of the last cart and get its storage type
            }


            function restoreCart(){
                for instance, if you want to restore the cart later on in your application
                    $order = \App\Order::find(1);
                    $savedCart = $order->saved_cart;
                    //you can restore the cart like this
                    $cart = $this->cart->restore($savedCart);
                    $cart->all();
                    $cart->getName();
            }
        }

#----------------------------------Events and listeners-----------------------
    After publishing the event assests as discribed above, you'd all the available events cart events.
    To code the listeners, open the listeners directory and edit the CartEventSubscriber class.
    You may have to study laravel documentation on events.

#------------------ Extending the package-------------------
        Run php artisan generate:cartstorage, and follow the instructions