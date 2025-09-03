<!DOCTYPE html>
<html lang="en" x-data="formApp()" x-init="init()">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tax Declaration Application - North Caloocan City Hall</title>
  <link rel="icon" href="{{ asset('img/mainlogo.png') }}" type="image/png">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <link rel="icon" href="{{ asset('img/mainlogo.png') }}" type="image/png">
  <!-- Font Awesome CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">

  <style>
    /* Smooth transitions */
    * {
      transition: all 0.2s ease;
    }

    /* Spinner */
    .spinner {
      animation: spin 1s linear infinite;
    }
    @keyframes spin {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }

    /* Pulse for loading text */
    .pulse-text {
      animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes pulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.5; }
    }

    /* Focus ring override */
    input:focus, button:focus {
      outline: none;
    }

    /* Better date input styling */
    input[type="date"] {
      padding-right: 0.75rem;
      background-image: none; 
    }

    input[type="date"]:focus {
      box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
      border-color: #3b82f6;
    }
  </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-gray-50 min-h-screen">

  <div class="max-w-2xl mx-auto mt-4 p-6 md:p-8">
    <div class="text-center mb-8">
      <img src="{{ asset('img/mainlogo.png') }}" alt="North Caloocan City Hall" class="w-16 h-16 mx-auto mb-3">
      <h1 class="text-3xl font-bold text-gray-800" data-i18n="tax_declaration_application">Tax Declaration Application</h1>
      <p class="text-gray-600 mt-2" data-i18n="complete_the_form">
        Complete the form to begin your pre-registration
      </p>
    </div>

    <form @submit.prevent="showModal" class="bg-white shadow-lg rounded-xl p-6 md:p-8 space-y-6">
      @csrf

      <!-- Language Selector - Centered Above Email -->
      <div class="mb-6">
        <div class="flex justify-center">
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 px-4 py-3 inline-flex items-center space-x-2 max-w-xs w-full">
            <span class="text-sm font-medium text-gray-700">🌐</span>
            <select id="language-select" 
                    class="flex-1 border-none text-sm focus:ring-0 focus:outline-none"
                    onchange="changeLang(this.value)">
              <option value="en">English</option>
              <option value="tl">Tagalog</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Email -->
      <div>
        <label class="block font-semibold text-gray-700 mb-2" data-i18n="email_address">Email Address <span class="text-red-500">*</span></label>
        <input type="email"
               x-model="form.email"
               @input="validateEmail"
               :class="{'border-red-500 ring-2 ring-red-200': !isEmailValid && form.email.length > 0, 'border-gray-300': isEmailValid || form.email.length === 0}"
               :placeholder="getPlaceholder('email_address')"
               class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 text-lg"
               required>
        <p x-show="!isEmailValid && form.email.length > 0" class="text-red-500 text-sm mt-1 flex items-center gap-1">
          <i class="fas fa-exclamation-circle"></i>
          <span data-i18n="email_error">Must end with <strong>@gmail.com</strong> or <strong>@yahoo.com</strong></span>
        </p>
      </div>

      <!-- Contact -->
      <div>
        <label class="block font-semibold text-gray-700 mb-2" data-i18n="contact_number">Contact Number <span class="text-red-500">*</span></label>
        <div class="flex items-center space-x-2">
          <img src="{{ asset('img/circle.png') }}" alt="Philippine Flag" class="w-6 h-4 object-cover rounded border">
          <input type="text" 
                 x-model="form.contact" 
                 @input="autoFormatContact(); validateContact()"
                 :class="{'border-red-500 ring-2 ring-red-200': !isContactValid && form.contact.length > 0, 'border-gray-300': isContactValid || form.contact.length === 0}"
                 class="flex-1 p-3 border rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 text-lg"
                 :placeholder="getPlaceholder('contact_number')"
                 maxlength="16" 
                 required>
        </div>
        <p x-show="!isContactValid && form.contact.length > 0" class="text-red-500 text-sm mt-1 flex items-center gap-1">
          <i class="fas fa-exclamation-circle"></i>
          <span data-i18n="contact_error">Must follow +63 format with exactly 10 digits</span>
        </p>
      </div>

      <!-- Name Fields -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block font-semibold text-gray-700 mb-2" data-i18n="first_name">First Name <span class="text-red-500">*</span></label>
          <input type="text" 
                 x-model="form.first_name" 
                 :placeholder="getPlaceholder('first_name')"
                 class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 text-lg"
                 required>
        </div>
        <div>
          <label class="block font-semibold text-gray-700 mb-2">
            <span data-i18n="middle_name">Middle Name</span>
            <span class="text-sm text-gray-500 ml-1" data-i18n="optional">(optional)</span>
          </label>
          <input type="text" 
                 x-model="form.middle_name" 
                 :placeholder="getPlaceholder('middle_name')"
                 class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 text-lg placeholder-gray-400">
        </div>
        <div>
          <label class="block font-semibold text-gray-700 mb-2" data-i18n="last_name">Last Name <span class="text-red-500">*</span></label>
          <input type="text" 
                 x-model="form.last_name" 
                 :placeholder="getPlaceholder('last_name')"
                 class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 text-lg"
                 required>
        </div>
      </div>

      <!-- Birthdate -->
      <div>
        <label class="block font-semibold text-gray-700 mb-2">
          <span data-i18n="date_of_birth">Date of Birth</span>
          <span class="text-red-500">*</span>
          <span class="text-sm text-gray-500 ml-1" data-i18n="must_be_18_years_old">(Must be 18+ years old)</span>
        </label>
        <input type="date" 
               x-model="form.birthdate"
               @change="validateBirthdate()"
               :min="minBirthdate"
               :max="maxBirthdate"
               :class="{'border-red-500 ring-2 ring-red-200': !isAgeValid && form.birthdate, 'border-gray-300': isAgeValid || !form.birthdate}"
               class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 text-lg bg-white"
               required>
        
              <!-- Age feedback -->
        <div x-show="form.birthdate" class="mt-2 text-sm">
          <span x-show="isAgeValid" class="text-green-600 font-medium flex items-center gap-1">
            <i class="fas fa-check-circle"></i>
            <span data-i18n="age_is">Age:</span> <span x-text="calculatedAge"></span> <span data-i18n="years_old">years old</span>
          </span>
          <span x-show="!isAgeValid" class="text-red-500 font-medium flex items-center gap-1">
            <i class="fas fa-times-circle"></i>
            <span data-i18n="must_be_18_or_older">Must be 18 or older (Currently</span> <span x-text="calculatedAge"></span> <span>)</span>
          </span>
        </div>

      <!-- PWD Beneficiary -->
      <div>
        <label class="block font-semibold text-gray-700 mb-3 mt-4" data-i18n="pwd_beneficiary">Are you a PWD Beneficiary? <span class="text-red-500">*</span></label>
        <div class="flex flex-wrap gap-6">
          <label class="flex items-center space-x-3 cursor-pointer">
            <input type="radio" value="yes" x-model="form.is_pwd" @change="onPwdChange" class="w-4 h-4 text-blue-600">
            <span class="font-medium" data-i18n-label="yes">Yes</span>
          </label>
          <label class="flex items-center space-x-3 cursor-pointer">
            <input type="radio" value="no" x-model="form.is_pwd" @change="onPwdChange" class="w-4 h-4 text-blue-600">
            <span class="font-medium" data-i18n-label="no">No</span>
          </label>
        </div>
      </div>

      <!-- PWD ID -->
      <div x-show="form.is_pwd === 'yes'" x-transition>
        <label class="block font-semibold text-gray-700 mb-2 mt-4" data-i18n="pwd_id">PWD ID <span class="text-red-500">*</span></label>
        <input type="text" 
              x-model="form.pwd_id" 
              @input="formatPWDId"
              class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 text-lg"
              :placeholder="getPlaceholder('pwd_id')"
              maxlength="19" 
              :required="form.is_pwd === 'yes'">
        <p class="text-sm text-blue-600 mt-1 flex items-center gap-1">
          <i class="fas fa-info-circle"></i>
          <span data-i18n="pwd_priority">PWD beneficiaries get priority in the queue</span>
        </p>
      </div>

      <!-- Senior Citizen ID -->
      <div x-show="isSenior" x-transition>
        <label class="block font-semibold text-gray-700 mb-2 mt-4" data-i18n="senior_citizen_id">Senior Citizen ID</label>
        <span class="text-sm text-gray-500 ml-1" data-i18n="optional">(optional)</span>
        <input type="text" 
               x-model="form.senior_id"
               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 text-lg mt-1"
               :placeholder="getPlaceholder('senior_citizen_id')">
        <p class="text-sm text-blue-600 mt-1 flex items-center gap-1">
          <i class="fas fa-info-circle"></i>
          <span data-i18n="senior_priority">Providing your Senior ID gives you priority in the queue</span>
        </p>
      </div>

      <!-- Submit Button -->
      <div class="pt-6">
        <button type="submit"
                :disabled="!isFormValid || isSubmitting"
                :class="{
                  'bg-gray-400 cursor-not-allowed': !isFormValid || isSubmitting,
                  'bg-blue-600 hover:bg-blue-700': isFormValid && !isSubmitting
                }"
                class="w-full text-white py-3 rounded-lg text-lg font-semibold shadow transition-colors flex items-center justify-center gap-2">
          
          <span x-show="!isSubmitting" data-i18n="submit_application">Submit Application</span>
          <span x-show="isSubmitting" class="flex items-center gap-2">
            <svg class="spinner w-5 h-5" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span data-i18n="submitting">Submitting...</span>
          </span>
        </button>
      </div>
    </form>
  </div>

  <!-- Confirmation Modal -->
  <div x-show="showingModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full p-6">
      <h2 class="text-2xl font-bold text-center text-gray-800 mb-6" data-i18n="review_your_details">Review Your Details</h2>
      
      <div class="space-y-3 text-sm">
        <div class="flex justify-between py-2 border-b border-gray-100">
          <strong class="text-gray-600" data-i18n="full_name">Full Name:</strong>
          <span x-text="form.first_name + ' ' + (form.middle_name ? form.middle_name + ' ' : '') + form.last_name" class="font-medium"></span>
        </div>
        <div class="flex justify-between py-2 border-b border-gray-100">
          <strong class="text-gray-600" data-i18n="email">Email:</strong>
          <span x-text="form.email" class="font-medium"></span>
        </div>
        <div class="flex justify-between py-2 border-b border-gray-100">
          <strong class="text-gray-600" data-i18n="contact">Contact:</strong>
          <span x-text="form.contact" class="font-medium"></span>
        </div>
        <div class="flex justify-between py-2 border-b border-gray-100">
          <strong class="text-gray-600" data-i18n="birthdate">Birthdate:</strong>
          <span><span x-text="formatDate(form.birthdate)"></span> (<span x-text="calculatedAge"></span> yrs)</span>
        </div>
        <template x-if="isSenior && form.senior_id">
          <div class="flex justify-between py-2 border-b border-gray-100">
            <strong class="text-gray-600" data-i18n="senior_id">Senior ID:</strong>
            <span x-text="form.senior_id" class="text-blue-600 font-medium"></span>
          </div>
        </template>
        <div class="flex justify-between py-2 border-b border-gray-100">
          <strong class="text-gray-600" data-i18n="pwd">PWD:</strong>
          <span x-text="form.is_pwd === 'yes' ? 'Yes' : 'No'" class="font-medium"></span>
        </div>
        <template x-if="form.is_pwd === 'yes'">
          <div class="flex justify-between py-2 border-b border-gray-100">
            <strong class="text-gray-600" data-i18n="pwd_id_field">PWD ID:</strong>
            <span x-text="form.pwd_id" class="text-blue-600 font-medium"></span>
          </div>
        </template>
      </div>

      <div class="flex justify-end gap-3 mt-6">
        <button @click="showingModal = false" 
                :disabled="isSubmitting"
                class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded text-sm font-medium transition">
          <span data-i18n="go_back">Go Back</span>
        </button>
        <button @click="submitForm" 
                :disabled="isSubmitting"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm font-medium flex items-center gap-2 transition">
          <span x-show="!isSubmitting" data-i18n="confirm_submit">Confirm & Submit</span>
          <span x-show="isSubmitting" class="flex items-center gap-1">
            <svg class="spinner w-4 h-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span data-i18n="processing">Processing...</span>
          </span>
        </button>
      </div>
    </div>
  </div>

  <!-- Loading Modal -->
  <div x-show="isSubmitting" x-transition class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-8 text-center">
      <svg class="spinner w-16 h-16 mx-auto text-blue-600 mb-4" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
      <p class="text-lg text-gray-700 mb-2" data-i18n="processing_application">Processing your application...</p>
      <div class="w-full bg-gray-200 rounded-full h-2">
        <div class="bg-blue-600 h-2 rounded-full" :style="`width: ${loadingProgress}%`"></div>
      </div>
    </div>
  </div>

  <!-- Thank You Modal -->
  <div x-show="thankYouModal" x-transition class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 text-center">
      <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <i class="fas fa-check text-green-600 text-2xl"></i>
      </div>
      <h2 class="text-2xl font-bold text-green-600 mb-2" data-i18n="application_submitted">Application Submitted!</h2>
      <p class="text-gray-600 mb-6" data-i18n="check_email_qr">Check your email for the QR code (valid for 24 hours).</p>
      <button @click="window.location.href='{{ url('/') }}'" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition">
        <span data-i18n="return_home">Return Home</span>
      </button>
    </div>
  </div>

  <!-- Age Error Modal -->
  <div x-show="isAgeErrorModal" x-transition class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 text-center">
      <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
      </div>
      <h2 class="text-xl font-bold text-red-600 mb-2" data-i18n="age_requirement_not_met">Age Requirement Not Met</h2>
      <p class="text-gray-600 mb-6" data-i18n="must_be_at_least_18">You must be at least 18 years old to submit this application.</p>
      <button @click="isAgeErrorModal = false" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition">
        <span data-i18n="ok">OK</span>
      </button>
    </div>
  </div>

  <!-- Alpine.js Logic -->
  <script>
    // Load Laravel Translations
    const translations = {
      en: @json(__('taxdec')),
      tl: @json(__('taxdec', [], 'tl'))
    };

    function changeLang(lang) {
      document.querySelectorAll('[data-i18n]').forEach(el => {
        const key = el.getAttribute('data-i18n');
        if (translations[lang][key] !== undefined) {
          el.textContent = translations[lang][key];
        }
      });

      document.querySelectorAll('[data-i18n-label]').forEach(el => {
        const key = el.getAttribute('data-i18n-label');
        if (translations[lang][key] !== undefined) {
          el.textContent = translations[lang][key];
        }
      });

      document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
        const key = el.getAttribute('data-i18n-placeholder');
        if (translations[lang][key] !== undefined) {
          el.placeholder = translations[lang][key];
        }
      });

      localStorage.setItem('siteLang', lang);
    }

    function getPlaceholder(key) {
      const lang = localStorage.getItem('siteLang') || 'en';
      return translations[lang][key] || key;
    }

    // Load saved language on page load
    document.addEventListener('DOMContentLoaded', () => {
      const savedLang ='en';
      document.getElementById('language-select').value = savedLang;
      changeLang(savedLang);
    });

    // Alpine.js App
    function formApp() {
      return {
        form: {
          email: '', contact: '', first_name: '', middle_name: '', last_name: '',
          birthdate: '', age: null, is_pwd: 'no', pwd_id: '', senior_id: '', is_preapplied: 1,
          service_type: ''
        },
        showingModal: false,
        thankYouModal: false,
        isEmailValid: true,
        isContactValid: true,
        isAgeValid: true,
        isSenior: false,
        isAgeErrorModal: false,
        calculatedAge: null,
        minBirthdate: '',
        maxBirthdate: '',
        isSubmitting: false,
        loadingProgress: 0,

        init() {
          const params = new URLSearchParams(window.location.search);
          this.form.service_type = params.get('service_type') || 'Tax Declaration';

          const today = new Date();
          this.minBirthdate = new Date(today.getFullYear() - 100, 0, 1).toISOString().split('T')[0];
          this.maxBirthdate = new Date(today.getFullYear() - 18, 11, 31).toISOString().split('T')[0];
        },

        startLoadingAnimation() {
          this.loadingProgress = 0;
          let interval = setInterval(() => {
            if (this.loadingProgress < 95) {
              this.loadingProgress += Math.random() * 10 + 3;
            } else {
              clearInterval(interval);
            }
          }, 500);
        },

        completeLoadingAnimation() {
          this.loadingProgress = 100;
          setTimeout(() => this.isSubmitting = false, 600);
        },

        validateBirthdate() {
          if (!this.form.birthdate) return;
          const birth = new Date(this.form.birthdate);
          const today = new Date();
          let age = today.getFullYear() - birth.getFullYear();
          if (today.getMonth() < birth.getMonth() || (today.getMonth() === birth.getMonth() && today.getDate() < birth.getDate())) age--;
          this.calculatedAge = age;
          this.form.age = age;
          this.isAgeValid = age >= 18;
          this.isSenior = age >= 60;
          if (!this.isSenior) this.form.senior_id = '';
        },

        onPwdChange() {
          if (this.form.is_pwd !== 'yes') this.form.pwd_id = '';
        },

        autoFormatContact() {
          let raw = this.form.contact.replace(/\D/g, '');
          if (raw.startsWith('63')) raw = raw.slice(2);
          if (raw.startsWith('0')) raw = raw.slice(1);
          raw = raw.slice(0, 10);
          let formatted = raw.replace(/(\d{3})(\d{3})(\d{4})/, (_, a, b, c) => `${a} ${b} ${c}`);
          this.form.contact = '+63 ' + formatted;
        },

        validateContact() {
          const pattern = /^\+63\s\d{3}\s\d{3}\s\d{4}$/;
          this.isContactValid = pattern.test(this.form.contact);
        },

        formatPWDId() {
          let raw = this.form.pwd_id.replace(/\W/g, '').toUpperCase();
          let parts = [raw.slice(0, 2), raw.slice(2, 6), raw.slice(6, 9), raw.slice(9, 16)];
          this.form.pwd_id = parts.filter(Boolean).join('-');
        },

        validateEmail() {
          const email = this.form.email.toLowerCase();
          this.isEmailValid = email.endsWith('@gmail.com') || email.endsWith('@yahoo.com');
        },

        formatDate(dateString) {
          if (!dateString) return '';
          const date = new Date(dateString);
          return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
        },

        get isFormValid() {
          const basicFieldsValid = this.isEmailValid && this.isContactValid && this.isAgeValid &&
                this.form.email && this.form.contact && this.form.first_name && this.form.last_name && 
                this.form.birthdate && this.form.is_pwd;
          
          const pwdFieldValid = this.form.is_pwd === 'no' || 
                              (this.form.is_pwd === 'yes' && this.form.pwd_id && this.form.pwd_id.trim() !== '');
          
          return basicFieldsValid && pwdFieldValid;
        },

        showModal() {
          if (!this.isFormValid) {
            if (!this.isAgeValid) {
              this.isAgeErrorModal = true;
            } else {
              alert(this.getTranslation('Please fill out all required fields correctly.'));
            }
            return;
          }
          this.showingModal = true;
        },

        async submitForm() {
          this.showingModal = false;
          this.isSubmitting = true;
          this.startLoadingAnimation();

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
            this.completeLoadingAnimation();

            if (res.ok && data.success) {
              setTimeout(() => this.thankYouModal = true, 600);
            } else {
              let message = data.message || 'Submission failed.';
              if (data.errors) message = Object.values(data.errors).flat().join('\n');
              alert(message);
            }
          } catch (err) {
            this.completeLoadingAnimation();
            setTimeout(() => alert('Failed to connect. Please check your internet connection.'), 600);
          }
        },

        getTranslation(key) {
          const lang = localStorage.getItem('siteLang') || 'en';
          return translations[lang][key] || key;
        }
      };
    }
  </script>
</body>
</html>