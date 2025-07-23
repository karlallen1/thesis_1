<!DOCTYPE html>
<html lang="en" x-data="formApp()" x-init="init()">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Non-Property Holdings Application</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/alpinejs" defer></script>
</head>
<body class="bg-gray-100 font-sans text-gray-800">
  <div class="max-w-xl mx-auto mt-12 p-6 bg-white shadow-xl rounded-xl">
    <h1 class="text-3xl font-bold text-center text-blue-700 mb-6">Non-Property Holdings Application Form</h1>
    <form @submit.prevent="showModal" class="space-y-5">
      @csrf

      <!-- Email -->
      <div>
        <label class="block font-semibold mb-1">Email</label>
        <input type="email" x-model="form.email" placeholder="juan@example.com"
          class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-lg" required>
      </div>

      <!-- Contact -->
      <div>
        <label class="block font-semibold mb-1">Contact Number</label>
        <input type="text" x-model="form.contact" @input="autoFormatContact"
          class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-lg"
          placeholder="+63 912 345 6789" maxlength="16" required>
      </div>

      <!-- Name Fields -->
      <div>
        <label class="block font-semibold mb-1">First Name</label>
        <input type="text" x-model="form.first_name" placeholder="Juan"
          class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-lg" required>
      </div>
      <div>
        <label class="block font-semibold mb-1">Middle Name</label>
        <input type="text" x-model="form.middle_name" placeholder="Reyes"
          class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-lg">
      </div>
      <div>
        <label class="block font-semibold mb-1">Last Name</label>
        <input type="text" x-model="form.last_name" placeholder="Dela Cruz"
          class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-lg" required>
      </div>

      <!-- Birthdate -->
      <div>
        <label class="block font-semibold mb-1">Birthdate</label>
        <input type="text" x-model="form.birthdate" @input="autoFormatDate"
          class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-lg"
          placeholder="MM/DD/YYYY" maxlength="10" required>
      </div>

      <!-- Age -->
      <div>
        <label class="block font-semibold mb-1">Age</label>
        <input type="number" x-model="form.age" placeholder="65"
          class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-lg" required>
      </div>

      <!-- PWD -->
      <div>
        <label class="block font-semibold mb-1">Are you a PWD Beneficiary?</label>
        <div class="flex gap-6 mt-2 text-lg">
          <label class="flex items-center space-x-2">
            <input type="radio" value="yes" x-model="form.is_pwd"> <span>Yes</span>
          </label>
          <label class="flex items-center space-x-2">
            <input type="radio" value="no" x-model="form.is_pwd"> <span>No</span>
          </label>
        </div>
      </div>

      <!-- PWD ID -->
      <div x-show="form.is_pwd === 'yes'" x-transition>
        <label class="block font-semibold mt-4 mb-1">PWD ID</label>
        <input type="text" x-model="form.pwd_id" @input="formatPWDId"
          class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-lg"
          placeholder="RR-PPMM-BBB-NNNNNNN" maxlength="19">
      </div>

      <!-- Submit -->
      <div class="pt-4">
        <button type="submit"
          class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg text-lg font-semibold shadow">
          Submit Application
        </button>
      </div>
    </form>
  </div>

  <!-- ✅ Confirmation Modal -->
  <div x-show="showingModal" x-transition class="fixed inset-0 bg-black bg-opacity-50 z-40 flex items-center justify-center">
    <div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-lg mx-auto">
      <h2 class="text-xl font-bold mb-4 text-center">Review Your Details</h2>
      <div class="space-y-2 text-sm">
        <div><strong>Email:</strong> <span x-text="form.email"></span></div>
        <div><strong>Contact:</strong> <span x-text="form.contact"></span></div>
        <div><strong>Full Name:</strong> <span x-text="form.first_name + ' ' + form.middle_name + ' ' + form.last_name"></span></div>
        <div><strong>Birthdate:</strong> <span x-text="form.birthdate"></span></div>
        <div><strong>Age:</strong> <span x-text="form.age"></span></div>
        <div><strong>PWD Beneficiary:</strong> <span x-text="form.is_pwd === 'yes' ? 'Yes' : 'No'"></span></div>
        <template x-if="form.is_pwd === 'yes'">
          <div><strong>PWD ID:</strong> <span x-text="form.pwd_id"></span></div>
        </template>
        <div><strong>Service Type:</strong> <span x-text="form.service_type"></span></div>
      </div>
      <div class="flex justify-end gap-4 mt-6">
        <button @click="showingModal = false" class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded text-sm">Go Back</button>
        <button @click="submitForm" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm">Confirm & Submit</button>
      </div>
    </div>
  </div>

  <!-- ✅ Thank You Modal -->
  <div x-show="thankYouModal" x-transition class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-md text-center">
      <h2 class="text-2xl font-bold text-green-600 mb-4">Thank You!</h2>
      <p class="mb-6">Your application has been submitted successfully.<br>Check your email for your QR code.</p>
      <div class="flex justify-center gap-4">
        <button @click="thankYouModal = false; resetForm()" class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded">Submit Again</button>
        <button @click="window.location.href='{{ url('/') }}'" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Complete</button>
      </div>
    </div>
  </div>

  <!-- ✅ Alpine.js Logic -->
  <script>
    function formApp() {
      return {
        form: {
          email: '',
          contact: '',
          first_name: '',
          middle_name: '',
          last_name: '',
          birthdate: '',
          age: '',
          is_pwd: '',
          pwd_id: '',
          service_type: ''
        },
        showingModal: false,
        thankYouModal: false,

        init() {
          const params = new URLSearchParams(window.location.search);
          this.form.service_type = params.get('service_type') || 'Tax Declaration';
        },

        autoFormatContact() {
          let raw = this.form.contact.replace(/\D/g, '');
          if (raw.startsWith('63')) raw = raw.slice(2);
          if (raw.startsWith('0')) raw = raw.slice(1);
          raw = raw.slice(0, 10);
          let formatted = raw.replace(/(\d{3})(\d{3})(\d{0,4})/, (_, a, b, c) => [a, b, c].filter(Boolean).join(' '));
          this.form.contact = '+63 ' + formatted.trim();
        },

        autoFormatDate() {
          let val = this.form.birthdate.replace(/\D/g, '');
          if (val.length >= 3) val = val.slice(0, 2) + '/' + val.slice(2);
          if (val.length >= 6) val = val.slice(0, 5) + '/' + val.slice(5, 9);
          this.form.birthdate = val;
        },

        formatPWDId() {
          let raw = this.form.pwd_id.replace(/\W/g, '').toUpperCase();
          let parts = [raw.slice(0, 2), raw.slice(2, 6), raw.slice(6, 9), raw.slice(9, 16)];
          this.form.pwd_id = parts.filter(Boolean).join('-');
        },

        showModal() {
          this.showingModal = true;
        },

        async submitForm() {
          this.showingModal = false;
          try {
            const res = await fetch("{{ route('application.store') }}", {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
              },
              body: JSON.stringify(this.form),
            });

            const data = await res.json();

            if (res.ok && data.success) {
              this.thankYouModal = true;
            } else {
              alert(data.message || 'Submission failed. Please try again.');
            }
          } catch (err) {
            alert('Submission failed. Please check your internet connection or try again later.');
          }
        },

        resetForm() {
          this.form = {
            email: '',
            contact: '',
            first_name: '',
            middle_name: '',
            last_name: '',
            birthdate: '',
            age: '',
            is_pwd: '',
            pwd_id: '',
            service_type: this.form.service_type
          };
        }
      }
    }
  </script>
</body>
</html>
