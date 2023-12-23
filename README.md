<!-- Create new application -->
laravel new property-listing-api

<!-- Basics setup -->
Use these step for login, register and logout with validation and message.

<!-- Create AuthController -->
php artisan make:controller AuthController

<!--  Pass the details in the header -->
Accept              application/vnd.api+json
Content-Type        application/vnd.api+json
<!-- For login Add on -->
Authorization       Bearer 1|laravel_sanctum_eLyHl5lWhRvFV0IvFR76sxnpYBrbSz4hGvj6eCZXd544f20c

<!-- Add Routes in api.php -->
There are two routes

<!-- Public Routes -->
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/brokers', [BrokersController::class, 'index']);
Route::get('/brokers/{broker}', [BrokersController::class, 'show']);

Route::get('/properties', [PropertiesController::class, 'index']);
Route::get('/properties/{property}', [PropertiesController::class, 'show']);

<!-- Protected Routes -->
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::apiResource('/brokers', BrokersController::class)->only([
        'store', 'update', 'destroy'
    ]); 
    Route::apiResource('/properties', PropertiesController::class)->only([
        'store', 'update', 'destroy'
    ]); 
    Route::post('/logout', [AuthController::class, 'logout']);
});

<!-- Add method in the AuthController -->
php artisan make:controller AuthController
<!-- 
    class AuthController extends Controller
    {
        use HttpResponses;

        public function login(LoginUserRequest $request)
        {
            $request->validated($request->all());

            if (!Auth::attempt($request->only(['email', 'password']))) {
                return $this->error('', 'Credintials do not match', 401);
            }

            $user = User::where('email', $request->email)->first();

            return $this->success([
                'user' => $user,
                'token' => $user->createToken("API Token of " . $user->name)->plainTextToken
            ]);

        }

        public function register(StoreUserRequest $request)
        {
            $request->validated($request->all());

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            // return response()->json("This is my register method");
            return $this->success([
                'user' => $user,
                'token' => $user->createToken("API Token of " . $user->name)->plainTextToken
            ]);
        }

        public function logout()
        {
            Auth::user()->currentAccessToken()->delete();
            return $this->success([
                'message' => 'You have successfully been logged out and your token has been deleted.'
            ]);
        }
    }
-->

<!-- Create Trait for error and success message -->
namespace App\Traits;

<!-- 
    trait HttpResponses
    {
        protected function success($data, $message = null, $code = 200)
        {
            return response()->json([
                'status' => 'Request was succesful.',
                'message' => $message,
                'data' => $data
            ], $code);
        }

        protected function error($data, $message = null, $code)
        {
            return response()->json([
                'status' => 'Error has occurred...',
                'message' => $message,
                'data' => $data
            ], $code);
        }
    }
-->

<!-- Create request for validate a login request -->
php artisan make:request LoginUserRequest
<!-- 
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
                'email' => ['required', 'string', 'email', 'max:255'],
                'password' => ['required', 'string', 'min:6'] 
            ];
    }
-->

<!-- Create request for validate a register request -->
php artisan make:request StoreUserRequest
<!-- 
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'confirmed', Password::defaults()]
            ];
    }
-->

<!-- Create model and migration for broker -->
php artisan make:model Broker -m
<!-- 
        $table->id();
        $table->string('name')->required();
        $table->string('address')->required();
        $table->string('city')->required();
        $table->string('zip_code')->required();
        $table->integer('phone_number')->required();
        $table->integer('logo_path')->required();
        $table->timestamps();

        $table->unique(['name', 'zip_code', 'phone_number']);
 -->

<!-- Create model and migration for Property -->
php artisan make:model Property -m
<!-- 
        $table->id();
        $table->unsignedBigInteger('broker_id');
        $table->string('address')->required();
        // $table->enum('listing_type', ['1', '2', '3'])->default('2')->required();
        $table->enum('listing_type', [
            ListingTypeEnum::OPEN->value,
            ListingTypeEnum::SELL->value,
            ListingTypeEnum::EXECLUSIVE->value,
            ListingTypeEnum::NET->value
        ])->default(ListingTypeEnum::OPEN->value);
        $table->string('city')->required();
        $table->longText('zip_code')->required();
        $table->year('build_year')->required();
        $table->timestamps();

        $table->foreign('broker_id')
                ->references('id')
                ->on('brokers')
                ->cascadeOnDelete();

        $table->unique(['address', 'zip_code']);
 -->

<!-- Create Enums App\Enums\ListingTypeEnum -->
Create enum and get the value for the migrations
<!-- 
    <?php
    namespace App\Enums;

    enum ListingTypeEnum : string {
        case OPEN = 'Open Listing';
        case SELL = 'Sell Listing';
        case EXECLUSIVE = 'Execlusive Listing';
        case NET = 'Net Listing';
    }
 -->

<!-- Create Property Characteristic -->
php artisan make:model PropertyCharacteristic -m
<!-- 
        $table->unsignedBigInteger('property_id');
        $table->float('price', 10, 2)->required();
        $table->integer('bedrooms')->required();
        $table->integer('bathrooms')->required();
        $table->float('sqft')->required();
        $table->float('price_sqft', 10, 2)->required();
        $table->enum('property_type', [
                PropertyTypeEnum::SINGLE->value,
                PropertyTypeEnum::TOWNHOUSE->value,
                PropertyTypeEnum::MULTIFAMILY->value,
                PropertyTypeEnum::BUNGALOW->value
            ]);
        $table->enum('status', [
                PropertyStatusEnum::SOLD->value,
                PropertyStatusEnum::SALE->value,
                PropertyStatusEnum::HOLD->value
            ])->required();

        $table->timestamps();

        $table->foreign('property_id')
                ->references('id')
                ->on('properties')
                ->cascadeOnDelete();
 -->


<!-- Create controller for Brokers -->
php artisan make:controller BrokersController --resource 
<!-- 
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return BrokersResource::collection(Broker::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBrokerRequest $request)
    {
        $request->validated();
        $broker = Broker::create([
            'name'          => $request->name,
            'address'       => $request->address,
            'city'          => $request->city,
            'zip_code'      => $request->zip_code,
            'phone_number'  => $request->phone_number,
            'logo_path'     => $request->logo_path
        ]);
        return new BrokersResource($broker);
    }

    /**
     * Display the specified resource.
     */
    public function show(Broker $broker)
    {
        return new BrokersResource($broker);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Broker $broker)
    {
        $broker->update($request->only([
            'name', 'address', 'city', 'zip_code', 'phone_number', 'logo_path'
        ]));

        return new BrokersResource($broker);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Broker $broker)
    {
        $broker->delete();

        return response()->json([
            'success' => true,
            'message' => "Broker has been deleted from the database."
        ]);
    }
-->

<!-- Create Resource  -->
php artisan make:resource BrokersResource
<!-- 
    return [
            'id'            => (string)$this->id,
            'name'          => $this->name,
            'address'       => $this->address,
            'city'          => $this->city,
            'zip_code'      => $this->zip_code,
            'phone_number'  => (string)$this->phone_number,
            'logo_path'     => $this->logo_path
        ];
-->

<!-- Create StoreBrokerRequest -->
php artisan make:request StoreBrokerRequest
<!-- 
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => [$this->isPostRequest(), 'unique:brokers', 'max:255'],
            'address' => [$this->isPostRequest(), 'max:255'],
            'city' => [$this->isPostRequest()],
            'zip_code' => [$this->isPostRequest()],
            'phone_number' => [$this->isPostRequest(), 'numeric', 'digits:10'],
            'logo_path' => [$this->isPostRequest()]
        ];
    }

    private function isPostRequest()
    {
        return request()->isMethod('post') ? 'required' : 'sometimes';
    }
-->

<!-- Create controller -->
php artisan make:controller PropertiesController --resource

<!--
    
    public function index()
    {
        return PropertiesResource::collection(Property::all());
    }

    
    public function store(StorePropertyRequest $request)
    {
        $request->validated();

        $property = Property::create([
            'broker_id' => $request->broker_id,
            'address' => $request->address,
            'listing_type' => $request->listing_type,
            'city' => $request->city,
            'zip_code' => $request->zip_code,
            'discription' => $request->discription,
            'build_year' => $request->build_year
        ]);

        $property->characteristic()->create([
            'property_id' => $property->id,
            'price' => $request->price,
            'bedrooms' => $request->bedrooms,
            'bathrooms' => $request->bathrooms,
            'sqft' => $request->sqft,
            'price_sqft' => $request->price_sqft,
            'property_type' => $request->property_type,
            'status' => $request->status
        ]);

        return new PropertiesResource($property);
    }

    
    public function show(Property $property)
    {
        return new PropertiesResource($property);
    }

    
    public function update(Request $request, Property $property)
    {
        // dd($request->input());
        $property->update($request->only([
            'broker_id', 'address', 'listing_type', 'city', 'zip_code', 'discription', 'build_year'
        ]));

        $property->characteristic->where('property_id', $property->id)->update($request->only([
            'property_id', 'price', 'bedrooms', 'bathrooms', 'sqft', 'price_sqft', 'property_type', 'status'
        ]));

        return new PropertiesResource($property);
    }

    
    public function destroy(Property $property)
    {
        $property->delete();

        return response()->json([
            'success' => true,
            'message' => "Property has been deleted from the database."
        ]);
    }
-->

<!-- Create resource for property -->
php artisan make:resource PropertiesResource
<!-- 
    
    public function toArray(Request $request): array
    {
        // dd($this->broker_id);
        $broker = Broker::find($this->broker_id);
        return [
            'id' => (string)$this->id,
            'attributes' => [
                'address' => $this->address,
                'listing_type' => $this->listing_type,
                'city' => $this->city,
                'zip_code' => $this->zip_code,
                'description' => $this->discription,
                'build_year' => $this->build_year
            ],
            'characteristics' => [
                new CharacteristicsResource($this->characteristic),   // here this is point to the model calling relationship
            ],
            'broker' => [
                'name' => $broker->name,
                'address' => $broker->address,
                'phone_number' => $broker->phone_number,
                'zip_code' => $broker->zip_code
            ]
        ];
    }
-->

<!-- Create resource for characteristics -->
php artisan make:resource CharacteristicsResource
<!-- 
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'price' => $this->price,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'sqft' => $this->sqft,
            'price_sqft' => $this->price_sqft,
            'property_type' => $this->property_type,
            'status' => $this->status,
        ];
    }
-->

<!-- Create StorePropertyRequest -->
php artisan make:request StorePropertyRequest
<!-- 
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'address' => ['required', 'max:255'],
            'listing_type' => ['required'],
            'city' => ['required'],
            'zip_code' => ['required', 'numeric'],
            'discription' => ['required'],
            'build_year' => ['required'],
            'price' => ['required'],
            'bedrooms' => ['required'],
            'bathrooms' => ['required'],
            'sqft' => ['required'],
            'price_sqft' => ['required'],
            'property_type' => ['required'],
            'status' => ['required']
        ];
    }
-->