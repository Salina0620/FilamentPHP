<x-filament-widgets::widget>
    <x-filament::section>
        <div class="custom-widget bg-cover bg-center p-6 rounded-lg shadow-md">
            <h2>Welcome to CareCure Hospital</h2> <br>
            <p>
                Thank you for being a part of CareCure. As a valued member of our medical team, you play a crucial role
                in delivering high-quality care to our patients.
               
            </p> <br>
            <p>
                We are committed to fostering a collaborative environment where you can thrive professionally. If you
                need any support or resources, please reach out to our administrative team.
            </p> <br>
            <p>
                Keep up the great work and continue to make a positive impact on the lives of our patients.
            </p> <br>
            {{-- <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($this->getStats() as $stat)
                    <div class="stat-card p-4 bg-white bg-opacity-80 rounded-lg shadow-md">
                        <h3 class="text-lg font-bold">{{ $stat->getLabel() }}</h3>
                        <p class="text-4xl font-extrabold text-{{ $stat->getColor() }}">{{ $stat->getValue() }}</p>
                        <p class="text-sm text-gray-700">{{ $stat->getDescription() }}</p>
                    </div>
                @endforeach
            </div> --}}
        </div> <br>
        <style>
            .custom-widget {
                /* background-image: url('images/hospital.jpg'); */
                /* height: 300px; */


                padding: 2rem;
                border-radius: 0.75rem;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }

            .stat-card {
                background-color: rgba(255, 255, 255, 0.8);
                border-radius: 10px;
                padding: 30px;

            }

            h2 {
                margin-top: 10px;
                font-size: 60px;
                color: rgb(197, 190, 197);
                text-align: center
            }

            p {
                color: rgb(161, 158, 158);
            }
        </style>

        <p style="color: rgb(126, 163, 231)">
            Please fill out the additional fields below. This will include important information such as contact
            details, medical history, or professional qualifications.

        </p>
        <a href="{{ url('/admin/profile') }}" class="btn btn-primary">
            <button style="margin-top: 10px; background-color: rgb(143, 143, 220); color:aliceblue; padding:0.3rem;"
                type="submit">Complete Your Profile</button>
        </a>


    </x-filament::section>
</x-filament-widgets::widget>
