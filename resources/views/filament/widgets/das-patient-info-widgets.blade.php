<!-- resources/views/filament/widgets/das-patient-info-widgets.blade.php -->

<x-filament-widgets::widget>
    <x-filament::section>
        <div class="hospital-info">
            <h2>Welcome to CareCure Hospital</h2>
            <p> <br>
                At CareCure, we are dedicated to providing exceptional care and service. Our state-of-the-art facilities and highly skilled medical professionals ensure that you receive the best treatment possible. 
                From routine check-ups to specialized treatments, we are here to support your health and well-being every step of the way.
            </p> <br>
            <p>
                Our hospital offers a range of services including emergency care, outpatient services, and diagnostic imaging. We prioritize your comfort and convenience, and our team is committed to making your visit as smooth as possible.
            </p> <br>
            <p>
                For any inquiries or assistance, please do not hesitate to contact our support team or visit our help center.
            </p>
        </div>
        <br><br>
        
        <!-- Example of displaying patient profile information -->
{{-- <div class="patient-profile">
    <h3>Patient Profile</h3>
    <p>Name: {{ $user->name }}</p>
    <p>Age: {{ $user->age }}</p>
    <p>Address: {{ $user->address }}</p>
</div> --}}
<style>
    /* resources/css/widget.css */
.widget-content {
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    color: #ffffff;
    height: 350px;
}

h2{
    margin-top: 10px;
    font-size: 70px;
    color:rgb(141, 141, 145);
}

p{
    color: rgb(161, 158, 158);
}
.patient-profile {
    margin-top: 20px;
}

</style>

<p style="color: rgb(126, 163, 231)">
    Please fill out the additional fields below. This will include important information such as contact details, medical history, or professional qualifications.

</p>
<a href="{{ url('/admin/profile') }}" class="btn btn-primary">
    <button style="margin-top: 10px; background-color: rgb(143, 143, 220); color:aliceblue; padding:0.3rem;" type="submit">Complete Your Profile</button>
</a>


    </x-filament::section>
</x-filament-widgets::widget>