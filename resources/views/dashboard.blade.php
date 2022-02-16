<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12" id="shops">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <form class="flex items-center justify-center" v-on:submit.prevent="fetchShops">
                <x-input class="w-1/2 px-6 py-4" placeholder="Find a shop near you" v-model="shopName" />
                <x-button class="py-4 ml-4">Search</x-button>
            </form>

            <div class="mt-8 shadow-sm sm:rounded-lg">
                <div v-show="locationErrorMessage" class="text-center">@{{ locationErrorMessage }}</div>
                <div v-show="loading" class="text-center">Loading...</div>
                <div v-show="!loading" class="grid grid-cols-3 gap-4" style="display: none;">
                    <div class="p-6 bg-white border-b border-gray-200" v-for="shop in shops" :key="shop.id">
                        <div class="text-xl">@{{ shop.name }}</div>
                        <div class="mt-4 text-gray-500" v-if="shop.distance">@{{ parseInt(shop.distance).toLocaleString() }}m away</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('script')
        <script src="https://unpkg.com/vue@next"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
        <script>
            Vue.createApp({
                data() {
                    return {
                        shopName: "",
                        long: "",
                        lat: "",
                        shops: [],
                        loading: false,
                        locationErrorMessage: "",
                    }
                },
                methods: {
                    fetchShops() {
                        this.loading = true;
                        axios.get(`/dashboard`, {
                            params: {
                                shopName: this.shopName,
                                long: this.long,
                                lat: this.lat,
                            }
                        }).then(res => {
                            this.shops = res.data.shops;
                        }).finally(() => {
                            this.loading = false;
                        })
                    },

                    getLocation(closure) {
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition((position) => {
                                this.long = position.coords.longitude;
                                this.lat = position.coords.latitude;
                                this.locationErrorMessage = "";

                                closure()
                            }, (error) => {
                                if (error.code === 1) {
                                    this.locationErrorMessage = "Please allow location access.";
                                }
                            });
                        } else {
                            x.innerHTML = "Geolocation is not supported by this browser.";
                        }
                    },
                },
                mounted() {
                    this.getLocation(() => {
                        this.fetchShops();
                    });
                },
            }).mount('#shops');
        </script>
    @endpush
    <footer class="px-3 text-lg text-center text-white no-underline uppercase bg-gray-900 py- italilc"><a
            href="https://www.wesley.io.ke/">Wesley Sinde: Some rights reserved {{ now()->year }}</a></footer>
</x-app-layout>
