@include('dashboard.header')

<!-- Content wrapper scroll start -->
<div class="content-wrapper-scroll">

<!-- Main header ends -->

<!-- Content wrapper start -->
<div class="content-wrapper">
    <!-- Row start -->
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12">
  
			<h3>Visual Card</h3>
            <p>specifically Designed for online transactions</p>
		
            <div class="card">
                <div class="card-body">
                    <!-- Row start -->
                            @if (session('error'))
                              <div class="alert box-bdr-red alert-dismissible fade show text-red" role="alert">
															<b>Error!</b>{{ session('error') }}
											<button type="button" class="btn-close" data-bs-dismiss="alert"
																aria-label="Close"></button>
									</div>
                                    @elseif (session('status'))
									<div class="alert box-bdr-green alert-dismissible fade show text-green" role="alert">
															<b>Success!</b> {{ session('status') }}
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
									</div>
                                  @endif
                    @forelse($details as $details)
                                   <div class="card">
    <div class="container mt-4 d-flex justify-content-center main">
        <div class="card">
            <div class="px-3 pt-3">
                <label for="card number" class="d-flex justify-content-between">
                    <span class="labeltxt">CARD NUMBER</span>
                    <img decoding="async" src="images/card.png" width="25" class="image">
                </label>
                <input type="number" name="number"  class="form-control inputtxt" placeholder="{{implode(' ', str_split($details->card_number, 4))}}" readonly>
            </div>
            <div class="d-flex justify-content-between px-3 pt-4">
                 <div>
                     <label for="date" class="exptxt">Expiry</label>
                     <input type="number" name="number" value="{{\Carbon\Carbon::parse($details->card_expiry)->format('m/d')}}" class="form-control expiry" placeholder="{{\Carbon\Carbon::parse($details->card_expiry)->format('m/y')}}" readonly>
                 </div>
                 <div>
                    <label for="cvv" class="cvvtxt">CVV /CVC</label>
                    <input type="number" name="number" class="form-control cvv" placeholder="{{$details->card_cvc}}" readonly>
                </div>
            </div>
        </div>
    </div>
    @empty
                            <!-- Card start -->
                            <div class="card">
                                <div class="card-body">
                              
                                <i class="bi bi-plus-square"></i>
                                 <p>The Imperial Premium Virtual {{Auth::user()->id}}  Card is a digital payment card designed to facilitate frequent online shoppers with a secure and flexible alternative to physical payment cards. The virtual card is instantly issued open request</p>
		                       
                                    <div class="btn-group align-items-center">
                                        <a href="{{route('request.card', Auth::user()->id)}}" class="btn btn-success active" aria-current="page">Request for</a>
                                        <a href="{{route('request.card', Auth::user()->id)}}" class="btn btn-success">a New</a>
                                        <a href="{{route('request.card', Auth::user()->id)}}" class="btn btn-success">Visual Card</a>
                                    </div>
                                </div>
                            </div>
                            <!-- Card end -->
                        
                    <!-- Row end -->
                </div>
            </div>
        </div>
    </div>
    <!-- Row end -->
@endforelse
</div>
</div>
<!-- Content wrapper end -->

</div>

@include('dashboard.footer')
