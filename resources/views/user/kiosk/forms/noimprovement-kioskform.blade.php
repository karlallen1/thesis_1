<!DOCTYPE html>
<html lang="en" x-data="formApp()" x-init="init()">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>No Improvement Holdings Application</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/alpinejs" defer></script>
  <style>
    /* Custom date picker styles */
    input[type="date"] {
      position: relative;
      background: white;
    }
    
    input[type="date"]::-webkit-calendar-picker-indicator {
      background: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zM4 9h12v7H4V9z'/%3e%3c/svg%3e") no-repeat;
      background-size: 20px 20px;
      cursor: pointer;
      opacity: 0.7;
      transition: opacity 0.2s;
    }
    
    input[type="date"]::-webkit-calendar-picker-indicator:hover {
      opacity: 1;
    }
    
    input[type="date"]:focus {
      outline: none;
      ring: 2px;
      ring-color: #3b82f6;
    }
    
    /* Better date input styling */
    .date-input {
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zM4 9h12v7H4V9z'/%3e%3c/svg%3e");
      background-repeat: no-repeat;
      background-position: right 12px center;
      background-size: 20px 20px;
      padding-right: 48px;
    }
  </style>
</head>
<body class="bg-gray-100 font-sans text-gray-800">
  <div class="max-w-xl mx-auto mt-12 p-6 bg-white shadow-xl rounded-xl">
    <h1 class="text-3xl font-bold text-center text-blue-700 mb-6">No Improvement Application Form</h1>
    <form @submit.prevent="showModal" class="space-y-5">
      @csrf

      <input type="hidden" name="is_preapplied" :value="form.is_preapplied">

      <!-- Email -->
      <div>
        <label class="block font-semibold mb-1">Email</label>
        <input type="email"
          x-model="form.email"
          @input="validateEmail"
          :class="{'border-red-500 focus:ring-red-500': !isEmailValid && form.email.length > 0, 'border-gray-300': isEmailValid || form.email.length === 0}"
          placeholder="juan@gmail.com"
          class="w-full p-3 border rounded-lg focus:ring-2 text-lg"
          required>
        <p x-show="!isEmailValid && form.email.length > 0" class="text-red-500 text-sm mt-1">
          Email is incorrect. Must end with <strong>@gmail.com</strong> or <strong>@yahoo.com</strong> (case-sensitive)
        </p>
      </div>

      <!-- Contact -->
      <div>
        <label class="block font-semibold mb-1">Contact Number</label>
        <div class="flex items-center space-x-2">
          <img src="{{ asset('img/circle.png') }}" alt="Philippine Flag" class="w-6 h-4 object-cover rounded-sm border">
          <input type="text" x-model="form.contact" @input="autoFormatContact(); validateContact()"
            :class="{'border-red-500 focus:ring-red-500': !isContactValid && form.contact.length > 0, 'border-gray-300': isContactValid || form.contact.length === 0}"
            class="flex-1 p-3 border rounded-lg focus:ring-2 text-lg"
            placeholder="+63 912 345 6789" maxlength="16" required>
        </div>
        <p x-show="!isContactValid && form.contact.length > 0" class="text-red-500 text-sm mt-1">
          Contact number must follow +63 format and contain exactly 10 digits
        </p>
      </div>

      <!-- Name Fields -->
      <div>
        <label class="block font-semibold mb-1">First Name</label>
        <input type="text" x-model="form.first_name" placeholder="Juan"
          class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-lg" required>
      </div>
      <div>
        <label class="block font-semibold mb-1 text-gray-700">Middle Name <span class="text-sm text-gray-500">(optional)</span></label>
        <input type="text" x-model="form.middle_name" placeholder="Reyes"
          class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-lg text-gray-700 placeholder-gray-400 opacity-80">
      </div>
      <div>
        <label class="block font-semibold mb-1">Last Name</label>
        <input type="text" x-model="form.last_name" placeholder="Dela Cruz"
          class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-lg" required>
      </div>

      <!-- Birthdate - Improved -->
      <div>
        <label class="block font-semibold mb-1">
          Birthdate 
          <span class="text-sm font-normal text-gray-600">(Must be 18 years or older)</span>
        </label>
        <input type="date" 
          x-model="form.birthdate"
          @change="validateBirthdate()"
          :min="minBirthdate"
          :max="maxBirthdate"
          :class="{'border-red-500 focus:ring-red-500': !isAgeValid && form.birthdate, 'border-gray-300': isAgeValid || !form.birthdate}"
          class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 text-lg date-input bg-white"
          required>
        
        <!-- Age feedback -->
        <div x-show="form.birthdate" class="mt-2 text-sm">
          <span x-show="calculatedAge && isAgeValid" class="text-green-600 font-medium">
            ✓ Age: <span x-text="calculatedAge"></span> years old
          </span>
          <span x-show="calculatedAge && !isAgeValid" class="text-red-500 font-medium">
            ✗ Must be 18 or older (Currently <span x-text="calculatedAge"></span> years old)
          </span>
        </div>
      </div>

      <!-- Senior ID - Only show if 60+ -->
      <div x-show="isSenior" x-transition>
        <label class="block font-semibold mt-4 mb-1">
          Senior Citizen ID 
          <span class="text-sm text-gray-500">(optional - for priority queue)</span>
        </label>
        <input type="text" x-model="form.senior_id"
          class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-lg"
          placeholder="Enter Senior Citizen ID">
        <p class="text-sm text-blue-600 mt-1">
          💡 Providing your Senior ID gives you priority in the queue
        </p>
      </div>

      <!-- PWD -->
      <div>
        <label class="block font-semibold mb-1 mt-4">Are you a PWD Beneficiary?</label>
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
        <label class="block font-semibold mt-4 mb-1">
          PWD ID
          <span class="text-sm text-gray-500">(for priority queue)</span>
        </label>
        <input type="text" x-model="form.pwd_id" @input="formatPWDId"
          class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-lg"
          placeholder="RR-PPMM-BBB-NNNNNNN" maxlength="19">
        <p class="text-sm text-blue-600 mt-1">
          💡 PWD beneficiaries get priority in the queue
        </p>
      </div>

      <!-- Submit -->
      <div class="pt-4">
        <button type="submit"
          :disabled="!isFormValid"
          :class="isFormValid ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-400 cursor-not-allowed'"
          class="w-full text-white py-3 rounded-lg text-lg font-semibold shadow transition-colors">
          Submit Application
        </button>
      </div>
    </form>
  </div>

  <!-- Confirmation Modal -->
  <div x-show="showingModal" x-transition class="fixed inset-0 bg-black bg-opacity-50 z-40 flex items-center justify-center">
    <div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-lg mx-auto">
      <h2 class="text-xl font-bold mb-4 text-center">Review Your Details</h2>
      <div class="space-y-2 text-sm">
        <div><strong>Email:</strong> <span x-text="form.email"></span></div>
        <div><strong>Contact:</strong> <span x-text="form.contact"></span></div>
        <div><strong>Full Name:</strong> <span x-text="form.first_name + ' ' + (form.middle_name ? form.middle_name + ' ' : '') + form.last_name"></span></div>
        <div><strong>Birthdate:</strong> <span x-text="formatDate(form.birthdate)"></span> <span class="text-gray-600">(<span x-text="calculatedAge"></span> years old)</span></div>
        <template x-if="isSenior && form.senior_id">
          <div><strong>Senior ID:</strong> <span x-text="form.senior_id"></span> <span class="text-blue-600">(Priority Queue)</span></div>
        </template>
        <div><strong>PWD Beneficiary:</strong> <span x-text="form.is_pwd === 'yes' ? 'Yes' : 'No'"></span></div>
        <template x-if="form.is_pwd === 'yes'">
          <div><strong>PWD ID:</strong> <span x-text="form.pwd_id || 'Not provided'"></span> <span class="text-blue-600">(Priority Queue)</span></div>
        </template>
        <div><strong>Service Type:</strong> <span x-text="form.service_type"></span></div>
      </div>
      <div class="flex justify-end gap-4 mt-6">
        <button @click="showingModal = false" class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded text-sm">Go Back</button>
        <button @click="submitForm" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm">Confirm & Submit</button>
      </div>
    </div>
  </div>

  <!-- ✅ NEW: Queue Number Modal (Replaces Thank You Modal for Kiosk) -->
  <div x-show="showQueueModal" x-transition class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md text-center">
      <div class="mb-6">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
          </svg>
        </div>
        <h2 class="text-2xl font-bold text-green-600 mb-2">You're in the Queue!</h2>
        
        <!-- Queue Number Display -->
        <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-4 my-4">
          <div class="text-sm text-gray-600 mb-1">Your Queue Number</div>
          <div class="text-5xl font-bold text-blue-600" x-text="queueNumber"></div>
        </div>
        
        <!-- Additional Info -->
        <div class="space-y-2 text-sm text-gray-600">
          <p class="font-medium">📄 Your ticket is printing...</p>
          <div x-show="isPriority" class="text-blue-600 font-medium">
            ⭐ Priority Queue - You'll be served faster!
          </div>
          <div x-show="estimatedWait">
            🕒 Estimated wait: <span x-text="estimatedWait"></span>
          </div>
        </div>
      </div>
      
      <div class="flex justify-center gap-3">
        <button @click="showQueueModal = false; resetForm()" 
                class="bg-gray-300 hover:bg-gray-400 px-6 py-3 rounded-lg text-sm font-medium">
          New Application
        </button>
        <button @click="window.location.href='{{ url('/kiosk') }}'" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg text-sm font-medium">
          Back to Services
        </button>
      </div>
    </div>
  </div>

  <!-- Thank You Modal (Fallback) -->
  <div x-show="thankYouModal" x-transition class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-md text-center">
      <h2 class="text-2xl font-bold text-green-600 mb-4">Thank You!</h2>
      <p class="mb-6">Your application has been submitted successfully.</p>
      <div class="flex justify-center gap-4">
        <button @click="thankYouModal = false; resetForm()" class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded">Submit Again</button>
        <button @click="window.location.href='{{ url('/kiosk') }}'" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Complete</button>
      </div>
    </div>
  </div>

  <!-- Age Error Modal -->
  <div x-show="isAgeErrorModal" x-transition class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-md text-center">
      <h2 class="text-xl font-bold text-red-600 mb-4">Age Requirement Not Met</h2>
      <p class="mb-6">You must be at least 18 years old to submit this application.</p>
      <div class="flex justify-center">
        <button @click="isAgeErrorModal = false" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">OK</button>
      </div>
    </div>
  </div>

  <!-- Alpine.js Logic -->
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
          age: null,
          is_pwd: '',
          pwd_id: '',
          senior_id: '',
          is_preapplied: 0, // ✅ CHANGED: Kiosk = 0 (false)
          service_type: ''
        },
        showingModal: false,
        thankYouModal: false,
        showQueueModal: false, // ✅ NEW: Queue modal state
        queueNumber: null,     // ✅ NEW: Store queue number
        isPriority: false,     // ✅ NEW: Priority status
        estimatedWait: '',     // ✅ NEW: Estimated wait time
        isEmailValid: true,
        isContactValid: true,
        isAgeValid: true,
        isSenior: false,
        isAgeErrorModal: false,
        calculatedAge: null,
        maxBirthdate: '',
        minBirthdate: '',

        init() {
          const params = new URLSearchParams(window.location.search);
          this.form.service_type = params.get('service_type') || 'No Improvement Holdings';

          // Calculate date limits for birthdate
          const today = new Date();
          
          // Reasonable minimum (100 years ago) - oldest possible birthdate
          const minDate = new Date();
          minDate.setFullYear(today.getFullYear() - 100);
          this.minBirthdate = minDate.toISOString().split('T')[0];
          
          // Maximum birthdate (18 years ago from today) - must be at least 18
          const maxDate = new Date();
          maxDate.setFullYear(today.getFullYear() - 18);
          maxDate.setMonth(today.getMonth());
          maxDate.setDate(today.getDate());
          this.maxBirthdate = maxDate.toISOString().split('T')[0];
        },

        validateBirthdate() {
          if (!this.form.birthdate) {
            this.calculatedAge = null;
            this.isAgeValid = true;
            this.isSenior = false;
            return;
          }

          // Calculate age
          const birthDate = new Date(this.form.birthdate);
          const today = new Date();
          let age = today.getFullYear() - birthDate.getFullYear();
          const monthDiff = today.getMonth() - birthDate.getMonth();
          
          if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
          }

          this.calculatedAge = age;
          this.form.age = age;
          
          // Validate age (must be 18+)
          this.isAgeValid = age >= 18;
          
          // Check if senior (60+)
          this.isSenior = age >= 60;
          
          // Clear senior_id if not senior anymore
          if (!this.isSenior) {
            this.form.senior_id = '';
          }
        },

        autoFormatContact() {
          let raw = this.form.contact.replace(/\D/g, '');
          if (raw.startsWith('63')) raw = raw.slice(2);
          if (raw.startsWith('0')) raw = raw.slice(1);
          raw = raw.slice(0, 10);
          let formatted = raw.replace(/(\d{3})(\d{3})(\d{0,4})/, (_, a, b, c) => [a, b, c].filter(Boolean).join(' '));
          this.form.contact = '+63 ' + formatted.trim();
        },

        validateContact() {
          const pattern = /^\+63\d{10}$/;
          this.isContactValid = pattern.test(this.form.contact.replace(/\s/g, ''));
        },

        formatPWDId() {
          let raw = this.form.pwd_id.replace(/\W/g, '').toUpperCase();
          let parts = [raw.slice(0, 2), raw.slice(2, 6), raw.slice(6, 9), raw.slice(9, 16)];
          this.form.pwd_id = parts.filter(Boolean).join('-');
        },

        validateEmail() {
          const email = this.form.email;
          this.isEmailValid = email.endsWith('@gmail.com') || email.endsWith('@yahoo.com');
        },

        formatDate(dateString) {
          if (!dateString) return '';
          const date = new Date(dateString);
          return date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
          });
        },

        get isFormValid() {
          return this.isEmailValid && 
                 this.isContactValid && 
                 this.isAgeValid && 
                 this.form.email && 
                 this.form.contact && 
                 this.form.first_name && 
                 this.form.last_name && 
                 this.form.birthdate &&
                 this.form.is_pwd;
        },

        showModal() {
          if (!this.isFormValid) {
            if (!this.isAgeValid) {
              this.isAgeErrorModal = true;
            } else {
              alert('Please fill out all required fields correctly.');
            }
            return;
          }
          this.showingModal = true;
        },

        async submitForm() {
          this.showingModal = false;

          try {
            // ✅ FIXED: Use kiosk route instead of pre-registration route
            const res = await fetch("{{ route('queue.kiosk') }}", {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
              },
              body: JSON.stringify(this.form),
            });

            const data = await res.json();

            if (res.ok && data.success) {
              // ✅ UPDATED: Handle kiosk-specific response
              if (data.queue_number) {
                // Store queue information
                this.queueNumber = data.queue_number;
                this.isPriority = data.is_priority || false;
                this.estimatedWait = data.estimated_wait || '';
                
                // Show queue modal instead of generic thank you
                this.showQueueModal = true;
              } else {
                // Fallback to thank you modal
                this.thankYouModal = true;
              }
            } else {
              if (data.errors && data.errors.birthdate) {
                this.isAgeErrorModal = true;
              } else {
                let errorMessage = data.message || 'Submission failed. Please try again.';
                if (data.errors) {
                  errorMessage = Object.values(data.errors).flat().join('\n');
                }
                alert(errorMessage);
              }
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
            age: null,
            is_pwd: '',
            pwd_id: '',
            senior_id: '',
            is_preapplied: 0, // ✅ Keep kiosk value
            service_type: this.form.service_type
          };
          
          // Reset all state
          this.isEmailValid = true;
          this.isContactValid = true;
          this.isAgeValid = true;
          this.isSenior = false;
          this.calculatedAge = null;
          this.isAgeErrorModal = false;
          this.showQueueModal = false;
          this.queueNumber = null;
          this.isPriority = false;
          this.estimatedWait = '';
        }
      }
    }
  </script>
</body>
</html>