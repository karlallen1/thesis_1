<!DOCTYPE html>
<html lang="en" x-data="formApp()" x-init="init()">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tax Declaration Application</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f5f7fa;
      color: #333;
      margin: 0;
      padding: 2rem;
      min-height: 100vh;
    }

    .container {
      max-width: 800px;
      margin: 0 auto;
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      padding: 2rem;
    }

    h1 {
      font-size: 1.8rem;
      color: #1e3a8a;
      text-align: center;
      margin-bottom: 1.5rem;
      font-weight: 600;
    }

    .language-select {
      text-align: center;
      margin-bottom: 1.5rem;
    }

    .language-select label {
      font-weight: 600;
      color: #4b5563;
      margin-right: 0.75rem;
      font-size: 1rem;
    }

    .language-select select {
      padding: 0.5rem 1rem;
      border: 1px solid #d1d5db;
      border-radius: 6px;
      font-size: 1rem;
      background-color: white;
      color: #333;
      min-width: 180px;
      outline: none;
    }

    .language-select select:focus {
      border-color: #3b82f6;
      box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
    }

    .section {
      margin-bottom: 1.5rem;
    }

    label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 600;
      color: #4b5563;
      font-size: 1rem;
    }

    .input-field, .date-input {
      width: 100%;
      padding: 0.75rem 1rem;
      font-size: 1rem;
      color: #333;
      border: 1px solid #d1d5db;
      border-radius: 6px;
      background-color: white;
      transition: all 0.2s ease;
    }

    .input-field::placeholder,
    .date-input::placeholder {
      color: #9ca3af;
    }

    .input-field:focus,
    .date-input:focus {
      outline: none;
      border-color: #3b82f6;
      box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
    }

    .input-group {
      display: flex;
      gap: 1rem;
    }

    .input-group input {
      flex: 1;
    }

    .contact-group {
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .contact-group img {
      width: 30px;
      height: 20px;
      object-fit: cover;
      border-radius: 3px;
      border: 1px solid #d1d5db;
    }

    .calendar-container {
      position: relative;
    }

    .calendar-input {
      width: 100%;
      padding: 0.75rem 1rem;
      font-size: 1rem;
      color: #333;
      border: 1px solid #d1d5db;
      border-radius: 6px;
      background-color: white;
      cursor: pointer;
      transition: all 0.2s ease;
      position: relative;
    }

    .calendar-input:focus {
      outline: none;
      border-color: #3b82f6;
      box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
    }

    .calendar-input::after {
      content: '📅';
      position: absolute;
      right: 1rem;
      top: 50%;
      transform: translateY(-50%);
      font-size: 1.2rem;
      pointer-events: none;
    }

    .calendar-dropdown {
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      background: white;
      border: 1px solid #d1d5db;
      border-radius: 8px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
      z-index: 1000;
      margin-top: 0.5rem;
      padding: 1rem;
    }

    .calendar-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
      gap: 1rem;
    }

    .calendar-nav {
      background: none;
      border: none;
      font-size: 1.5rem;
      cursor: pointer;
      padding: 0.25rem 0.5rem;
      border-radius: 4px;
      color: #4b5563;
      transition: all 0.2s ease;
      min-width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .calendar-nav:hover {
      background-color: #f3f4f6;
      color: #1f2937;
    }

    .calendar-nav:disabled {
      opacity: 0.3;
      cursor: not-allowed;
    }

    .calendar-selects {
      display: flex;
      gap: 0.5rem;
      flex: 1;
      justify-content: center;
    }

    .calendar-select {
      padding: 0.5rem;
      border: 1px solid #d1d5db;
      border-radius: 4px;
      font-size: 0.9rem;
      background-color: white;
      color: #333;
      cursor: pointer;
    }

    .calendar-select:focus {
      outline: none;
      border-color: #3b82f6;
    }

    .calendar-grid {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      gap: 0.25rem;
    }

    .calendar-day-header {
      text-align: center;
      font-weight: 600;
      font-size: 0.8rem;
      color: #6b7280;
      padding: 0.5rem 0;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .calendar-day {
      aspect-ratio: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      border: none;
      background: none;
      cursor: pointer;
      border-radius: 6px;
      font-size: 0.9rem;
      transition: all 0.2s ease;
      color: #374151;
      font-weight: 500;
      min-height: 36px;
    }

    .calendar-day:hover:not(:disabled) {
      background-color: #e0e7ff;
      color: #3730a3;
    }

    .calendar-day.selected {
      background-color: #3b82f6;
      color: white;
      font-weight: 600;
    }

    .calendar-day.today {
      background-color: #fef3c7;
      color: #92400e;
      font-weight: 600;
    }

    .calendar-day.selected.today {
      background-color: #3b82f6;
      color: white;
    }

    .calendar-day.other-month {
      color: #9ca3af;
    }

    .calendar-day:disabled {
      color: #d1d5db;
      cursor: not-allowed;
    }

    .calendar-footer {
      margin-top: 1rem;
      padding-top: 1rem;
      border-top: 1px solid #e5e7eb;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .calendar-today-btn {
      background: none;
      border: 1px solid #d1d5db;
      padding: 0.5rem 1rem;
      border-radius: 6px;
      cursor: pointer;
      font-size: 0.9rem;
      color: #4b5563;
      transition: all 0.2s ease;
    }

    .calendar-today-btn:hover {
      background-color: #f9fafb;
      border-color: #9ca3af;
    }

    .calendar-close-btn {
      background-color: #3b82f6;
      color: white;
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 6px;
      cursor: pointer;
      font-size: 0.9rem;
      font-weight: 500;
      transition: all 0.2s ease;
    }

    .calendar-close-btn:hover {
      background-color: #2563eb;
    }

    .radio-group {
      display: flex;
      gap: 1rem;
      flex-wrap: wrap;
      margin-top: 0.5rem;
    }

    .radio-option {
      display: inline-flex;
      align-items: center;
      padding: 0.5rem 1rem;
      border: 1px solid #d1d5db;
      border-radius: 6px;
      cursor: pointer;
      background: #f9fafb;
      font-size: 1rem;
      transition: all 0.2s ease;
    }

    .radio-option:hover {
      background: #e2e8f0;
      border-color: #9ca3af;
    }

    .radio-option input[type="radio"] {
      margin-right: 0.5rem;
      transform: scale(1.2);
    }

    .radio-option input[type="radio"]:checked + span {
      font-weight: 600;
      color: #1e3a8a;
    }

    .info-tip {
      font-size: 0.875rem;
      color: #0284c7;
      margin-top: 0.5rem;
    }

    .info-tip span::before {
      content: "💡 ";
    }

    .submit-btn {
      display: block;
      width: 100%;
      padding: 1rem;
      background-color: #3b82f6;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 1.1rem;
      font-weight: 600;
      cursor: pointer;
      margin-top: 1.5rem;
      transition: background-color 0.2s ease;
    }

    .submit-btn:hover:not(:disabled) {
      background-color: #2563eb;
    }

    .submit-btn:disabled {
      background-color: #9ca3af;
      cursor: not-allowed;
      opacity: 1;
    }

    .error-text {
      color: #dc2626;
      font-size: 0.875rem;
      margin-top: 0.5rem;
    }

    .modal {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(0, 0, 0, 0.5);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }

    .modal-content {
      background: white;
      padding: 2.5rem;
      border-radius: 16px;
      width: 100%;
      max-width: 800px;
      text-align: center;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    }

    .modal-title {
      font-size: 1.8rem;
      margin-bottom: 1.5rem;
      color: #1e3a8a;
      font-weight: 600;
    }

    .modal-body {
      margin-bottom: 1.5rem;
      line-height: 1.8;
      font-size: 1.1rem;
      color: #333;
      text-align: left;
      padding: 0 1.5rem;
    }

    .modal-body p {
      margin: 0.75rem 0;
      font-size: 1.05rem;
    }

    .modal-body strong {
      color: #1f2937;
      font-weight: 600;
    }

    .modal-btn {
      background-color: #10b981;
      color: white;
      border: none;
      padding: 0.9rem 2rem;
      border-radius: 8px;
      font-size: 1.15rem;
      font-weight: 500;
      cursor: pointer;
      transition: background-color 0.2s;
    }

    .modal-btn:hover {
      background-color: #059669;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Tax Declaration Application</h1>

    <!-- Back Button -->
    <button 
      onclick="window.history.back()" 
      class="absolute top-12 left-12 z-20 bg-amber-600 hover:bg-amber-700 text-white font-bold px-6 py-3 rounded-lg shadow-lg transition-all duration-200"
    >
      ← BACK
    </button>

    <div class="language-select">
      <label for="language">Language:</label>
      <select id="language" x-model="selectedLanguage" @change="changeLanguage">
        <option value="en">English</option>
        <option value="tl">Filipino (Tagalog)</option>
      </select>
    </div>

    <form @submit.prevent="showModal" class="space-y-6" @click.outside="closeCalendar()">
      @csrf

      <div class="section">
        <label x-text="translations[selectedLanguage].fullName"></label>
        <div class="input-group">
          <input type="text" x-model="form.first_name" placeholder="First Name" class="input-field" required>
          <input type="text" x-model="form.last_name" placeholder="Last Name" class="input-field" required>
        </div>
      </div>

      <div class="section">
        <label x-text="translations[selectedLanguage].middleName"></label>
        <input type="text" x-model="form.middle_name" placeholder="Middle Name" class="input-field">
      </div>

      <div class="section">
        <label x-text="translations[selectedLanguage].birthdate"></label>
        <div class="calendar-container">
          <div class="calendar-input" @click="toggleCalendar()" style="position: relative;">
            <span x-text="form.birthdate ? formatDateDisplay(form.birthdate) : 'Select your date of birth'" 
                  :class="{'text-gray-400': !form.birthdate, 'text-black': form.birthdate}"></span>
            <span style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); font-size: 1.2rem; pointer-events: none;">📅</span>
          </div>
          
          <div x-show="showCalendar" x-transition class="calendar-dropdown" @click.outside="closeCalendar()">
            <div class="calendar-header">
              <button type="button" @click="previousMonth()" class="calendar-nav" :disabled="!canGoPrevious()">‹</button>
              <div class="calendar-selects">
                <select x-model="calendarMonth" @change="updateCalendar()" class="calendar-select">
                  <template x-for="(month, index) in months" :key="index">
                    <option :value="index" x-text="month"></option>
                  </template>
                </select>
                <select x-model="calendarYear" @change="updateCalendar()" class="calendar-select">
                  <template x-for="year in availableYears" :key="year">
                    <option :value="year" x-text="year"></option>
                  </template>
                </select>
              </div>
              <button type="button" @click="nextMonth()" class="calendar-nav" :disabled="!canGoNext()">›</button>
            </div>
            
            <div class="calendar-grid">
              <template x-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']" :key="day">
                <div class="calendar-day-header" x-text="day"></div>
              </template>
              
              <template x-for="day in calendarDays" :key="day.key">
                <button type="button" 
                        @click="selectDate(day)" 
                        :disabled="!day.selectable"
                        :class="{
                          'calendar-day': true,
                          'selected': day.selected,
                          'today': day.isToday,
                          'other-month': day.otherMonth
                        }"
                        x-text="day.day">
                </button>
              </template>
            </div>
            
            <div class="calendar-footer">
              <button type="button" @click="goToToday()" class="calendar-today-btn">Today</button>
              <button type="button" @click="closeCalendar()" class="calendar-close-btn">Done</button>
            </div>
          </div>
        </div>
        
        <div x-show="calculatedAge && !isAgeValid" class="error-text">
          <span x-text="translations[selectedLanguage].ageError"></span>
        </div>
        
        <div x-show="calculatedAge && isAgeValid" class="info-tip">
          <span>You are <strong x-text="calculatedAge"></strong> years old</span>
        </div>
      </div>

      <div class="section">
        <label x-text="translations[selectedLanguage].email"></label>
        <input type="email"
               x-model="form.email"
               @input="validateEmail"
               :class="{'border-red-500': !isEmailValid && form.email.length > 0}"
               class="input-field"
               placeholder="juan@gmail.com"
               required>
        <p x-show="!isEmailValid && form.email.length > 0" class="error-text">
          <span x-html="translations[selectedLanguage].emailError"></span>
        </p>
      </div>

      <div class="section">
        <label x-text="translations[selectedLanguage].contact"></label>
        <div class="contact-group">
          <img src="{{ asset('img/circle.png') }}" alt="Philippine Flag">
          <input type="text"
                 x-model="form.contact"
                 @input="autoFormatContact(); validateContact()"
                 :class="{'border-red-500': !isContactValid && form.contact.length > 0}"
                 class="input-field flex-1"
                 placeholder="+63 912 345 6789"
                 maxlength="16"
                 required>
        </div>
        <p x-show="!isContactValid && form.contact.length > 0" class="error-text">
          <span x-text="translations[selectedLanguage].contactError"></span>
        </p>
      </div>

      <div class="section">
        <label x-text="translations[selectedLanguage].pwdBeneficiary"></label>
        <div class="radio-group">
          <label class="radio-option">
            <input type="radio" value="yes" x-model="form.is_pwd" @change="onPwdChange()"> <span>Yes</span>
          </label>
          <label class="radio-option">
            <input type="radio" value="no" x-model="form.is_pwd" @change="onPwdChange()"> <span>No</span>
          </label>
        </div>
      </div>

      <div x-show="form.is_pwd === 'yes'" x-transition>
        <label x-text="translations[selectedLanguage].pwdId"></label>
        <input type="text" x-model="form.pwd_id" @input="formatPWDId" class="input-field" placeholder="RR-PPMM-BBB-NNNNNNN" maxlength="19">
        <div class="info-tip">
          <span x-text="translations[selectedLanguage].pwdInfo"></span>
        </div>
      </div>

      <div x-show="isSenior" x-transition>
        <label x-text="translations[selectedLanguage].seniorId"></label>
        <input type="text" x-model="form.senior_id" class="input-field" placeholder="Enter Senior Citizen ID">
        <div class="info-tip">
          <span x-text="translations[selectedLanguage].seniorInfo"></span>
        </div>
      </div>

      <button type="submit" :disabled="!isFormValid" class="submit-btn">
        <span x-text="translations[selectedLanguage].submitButton"></span>
      </button>
    </form>
  </div>

  <!-- Confirmation Modal -->
  <div x-show="showingModal" class="modal" x-transition>
    <div class="modal-content">
      <h2 class="modal-title" x-text="translations[selectedLanguage].reviewDetails"></h2>
      <div class="modal-body">
        <p><strong x-text="translations[selectedLanguage].fullName"></strong>: <span x-text="form.first_name + ' ' + (form.middle_name ? form.middle_name + ' ' : '') + form.last_name"></span></p>
        <p><strong x-text="translations[selectedLanguage].email"></strong>: <span x-text="form.email"></span></p>
        <p><strong x-text="translations[selectedLanguage].contact"></strong>: <span x-text="form.contact"></span></p>
        <p><strong x-text="translations[selectedLanguage].birthdate"></strong>: <span x-text="formatDate(form.birthdate)"></span></p>
        <p><strong x-text="translations[selectedLanguage].pwdBeneficiary"></strong>: <span x-text="form.is_pwd === 'yes' ? translations[selectedLanguage].yes : translations[selectedLanguage].no"></span></p>
        <p><strong x-text="translations[selectedLanguage].serviceType"></strong>: <span x-text="form.service_type"></span></p>
      </div>
      <button @click="submitForm" class="modal-btn">Confirm & Submit</button>
    </div>
  </div>

  <!-- Thank You Modal -->
  <div x-show="thankYouModal" class="modal" x-transition>
    <div class="modal-content">
      <h2 class="modal-title" x-text="translations[selectedLanguage].thankYou"></h2>
      <p class="modal-body" x-text="translations[selectedLanguage].thankYouMessage"></p>
      <button @click="thankYouModal = false; resetForm(); window.location.href='/kiosk'" class="modal-btn">Complete</button>
    </div>
  </div>

  <!-- Queue Success Modal -->
  <div x-show="showQueueModal" x-transition class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-md text-center">
      <h2 class="text-2xl font-bold text-green-600 mb-2">Welcome to the Queue!</h2>
      <p class="text-lg mb-4">Your ticket is printing...</p>
      <div class="bg-gray-100 p-4 rounded-lg mb-4">
        <div class="text-sm text-gray-600">Your Queue Number</div>
        <div class="text-4xl font-bold text-blue-600 mt-1" x-text="queueNumber"></div>
      </div>
      <div class="text-sm text-gray-600" x-show="isPriority">
        <span x-text="priorityType"></span> Priority
      </div>
      <button 
          @click="showQueueModal = false; resetForm(); window.location.href='/kiosk'"
          class="mt-6 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">
        Complete
      </button>
    </div>
  </div>

  <script>
    function formApp() {
      return {
        // Form data
        form: {
          email: '', contact: '', first_name: '', middle_name: '', last_name: '',
          birthdate: '', is_pwd: 'no', pwd_id: '', senior_id: '', service_type: ''
        },
        showingModal: false,
        thankYouModal: false,
        showQueueModal: false,
        queueNumber: null,
        applicationId: null, // ✅ Store ID for printing
        isPriority: false,
        priorityType: '',

        // Validation
        isEmailValid: true,
        isContactValid: true,
        isAgeValid: true,
        isSenior: false,
        calculatedAge: null,

        // Language
        selectedLanguage: 'en',
        translations: {
          en: {
            fullName: "Applicant Full Name",
            middleName: "Middle Name (optional)",
            birthdate: "Date of Birth",
            email: "Email Address",
            contact: "Contact Number",
            pwdBeneficiary: "Are you a PWD Beneficiary?",
            pwdId: "PWD ID",
            pwdInfo: "PWD beneficiaries get priority in the queue",
            seniorId: "Senior Citizen ID",
            seniorInfo: "Providing your Senior ID gives you priority in the queue",
            submitButton: "Submit Application",
            reviewDetails: "Review Your Details",
            thankYou: "Thank You!",
            thankYouMessage: "Your application has been submitted successfully.",
            ageError: "You must be at least 18 years old to submit this application.",
            emailError: "Email must end with <strong>@gmail.com</strong> or <strong>@yahoo.com</strong>",
            contactError: "Must follow +63 format with exactly 10 digits",
            yes: "Yes",
            no: "No",
            serviceType: "Service Type:"
          },
          tl: {
            fullName: "Buong Pangalan ng Tagapag-apply",
            middleName: "Gitnang Pangalan (opsyonal)",
            birthdate: "Petsa ng Kapanganakan",
            email: "Address ng Email",
            contact: "Numero ng Kontak",
            pwdBeneficiary: "Ikaw ba ay isang PWD Beneficiary?",
            pwdId: "PWD ID",
            pwdInfo: "Ang mga PWD beneficiaries ay may priyoridad sa pila",
            seniorId: "Senior Citizen ID",
            seniorInfo: "Ang pagbibigay ng inyong Senior ID ay nagbibigay sa inyo ng priyoridad sa pila",
            submitButton: "Isumite ang Aplikasyon",
            reviewDetails: "Suriin ang Inyong Detalye",
            thankYou: "Salamat!",
            thankYouMessage: "Matagumpay na naisumite ang inyong aplikasyon.",
            ageError: "Dapat ay hindi bababa sa 18 taong gulang upang isumite ang aplikasyon na ito.",
            emailError: "Dapat magtatapos sa <strong>@gmail.com</strong> o <strong>@yahoo.com</strong>",
            contactError: "Dapat sundin ang format na +63 at may eksaktong 10 digit",
            yes: "Oo",
            no: "Hindi",
            serviceType: "Uri ng Serbisyo:"
          }
        },

        // Calendar
        showCalendar: false,
        calendarMonth: new Date().getMonth(),
        calendarYear: new Date().getFullYear(),
        calendarDays: [],
        availableYears: [],
        months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],

        // Idle Timer
        idleTimer: null,

        init() {
          const params = new URLSearchParams(window.location.search);
          this.form.service_type = params.get('service_type') || 'Tax Declaration';

          const today = new Date();
          this.minBirthdate = new Date(today.getFullYear() - 100, 0, 1).toISOString().split('T')[0];
          this.maxBirthdate = new Date(today.getFullYear() - 18, 11, 31).toISOString().split('T')[0];

          this.initializeCalendar();
          this.updateCalendar();

          this.startIdleTimer();
        },

        startIdleTimer() {
          this.resetIdleTimer();
          ['mousemove', 'keydown', 'click', 'input'].forEach(event =>
            document.addEventListener(event, this.resetIdleTimer)
          );
        },

        resetIdleTimer: () => {
          const self = document.querySelector('[x-data]')._x_dataStack[0];
          clearTimeout(self.idleTimer);
          self.idleTimer = setTimeout(() => {
            self.returnToServices();
          }, 3 * 60 * 1000);
        },

        returnToServices() {
          ['mousemove', 'keydown', 'click', 'input'].forEach(event =>
            document.removeEventListener(event, this.resetIdleTimer)
          );
          this.resetForm();
          window.location.href = '/kiosk';
        },

        initializeCalendar() {
          const currentYear = new Date().getFullYear();
          const minYear = currentYear - 100;
          const maxYear = currentYear - 18;
          this.availableYears = [];
          for (let year = maxYear; year >= minYear; year--) this.availableYears.push(year);
          this.calendarYear = maxYear;
        },

        toggleCalendar() {
          this.showCalendar = !this.showCalendar;
          if (this.showCalendar) {
            if (this.form.birthdate) {
              const date = new Date(this.form.birthdate);
              this.calendarMonth = date.getMonth();
              this.calendarYear = date.getFullYear();
            }
            this.updateCalendar();
          }
        },

        closeCalendar() {
          this.showCalendar = false;
        },

        updateCalendar() {
          const firstDay = new Date(this.calendarYear, this.calendarMonth, 1);
          const lastDay = new Date(this.calendarYear, this.calendarMonth + 1, 0);
          const startDate = new Date(firstDay);
          startDate.setDate(startDate.getDate() - firstDay.getDay());

          const today = new Date();
          const selectedDate = this.form.birthdate ? new Date(this.form.birthdate) : null;
          const minDate = new Date(this.calendarYear - 100, 0, 1);
          const maxDate = new Date();
          maxDate.setFullYear(maxDate.getFullYear() - 18, 11, 31);

          this.calendarDays = [];
          for (let i = 0; i < 42; i++) {
            const date = new Date(startDate);
            date.setDate(startDate.getDate() + i);
            const isCurrentMonth = date.getMonth() === this.calendarMonth;
            const isToday = date.toDateString() === today.toDateString();
            const isSelected = selectedDate && date.toDateString() === selectedDate.toDateString();
            const isSelectable = date >= minDate && date <= maxDate;

            this.calendarDays.push({
              day: date.getDate(),
              date: new Date(date),
              isToday,
              selected: isSelected,
              otherMonth: !isCurrentMonth,
              selectable: isSelectable,
              key: `${date.getFullYear()}-${date.getMonth()}-${date.getDate()}`
            });
          }
        },

        selectDate(day) {
          if (!day.selectable) return;
          const dateStr = day.date.toISOString().split('T')[0];
          this.form.birthdate = dateStr;
          this.validateBirthdate();
          this.closeCalendar();
        },

        previousMonth() { this.calendarMonth === 0 ? (this.calendarMonth = 11, this.calendarYear--) : this.calendarMonth--; this.updateCalendar(); },
        nextMonth() { this.calendarMonth === 11 ? (this.calendarMonth = 0, this.calendarYear++) : this.calendarMonth++; this.updateCalendar(); },
        canGoPrevious() { const minYear = new Date().getFullYear() - 100; return this.calendarYear > minYear || (this.calendarYear === minYear && this.calendarMonth > 0); },
        canGoNext() { const maxYear = new Date().getFullYear() - 18; return this.calendarYear < maxYear || (this.calendarYear === maxYear && this.calendarMonth < 11); },
        goToToday() { const today = new Date(); const maxDate = new Date(); maxDate.setFullYear(maxDate.getFullYear() - 18, 11, 31); if (today <= maxDate) { this.calendarMonth = today.getMonth(); this.calendarYear = today.getFullYear(); } else { this.calendarMonth = 11; this.calendarYear = maxDate.getFullYear(); } this.updateCalendar(); },

        formatDateDisplay(dateString) {
          if (!dateString) return '';
          const date = new Date(dateString);
          return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
        },

        changeLanguage() {},

        validateBirthdate() {
          if (!this.form.birthdate) return;
          const birth = new Date(this.form.birthdate);
          const today = new Date();
          let age = today.getFullYear() - birth.getFullYear();
          if (today.getMonth() < birth.getMonth() || (today.getMonth() === birth.getMonth() && today.getDate() < birth.getDate())) age--;
          this.calculatedAge = age;
          this.isAgeValid = age >= 18;
          this.isSenior = age >= 60;
          if (!this.isSenior) this.form.senior_id = '';
        },

        onPwdChange() { if (this.form.is_pwd !== 'yes') this.form.pwd_id = ''; },
        autoFormatContact() { let raw = this.form.contact.replace(/\D/g, ''); if (raw.startsWith('63')) raw = raw.slice(2); if (raw.startsWith('0')) raw = raw.slice(1); raw = raw.slice(0, 10); let formatted = raw.replace(/(\d{3})(\d{3})(\d{4})/, (_, a, b, c) => `${a} ${b} ${c}`); this.form.contact = '+63 ' + formatted; },
        validateContact() { const pattern = /^\+63\s\d{3}\s\d{3}\s\d{4}$/; this.isContactValid = pattern.test(this.form.contact); },
        formatPWDId() { let raw = this.form.pwd_id.replace(/\W/g, '').toUpperCase(); let parts = [raw.slice(0, 2), raw.slice(2, 6), raw.slice(6, 9), raw.slice(9, 16)]; this.form.pwd_id = parts.filter(Boolean).join('-'); },
        validateEmail() { const email = this.form.email.toLowerCase(); this.isEmailValid = email.endsWith('@gmail.com') || email.endsWith('@yahoo.com'); },
        formatDate(dateString) { if (!dateString) return ''; const date = new Date(dateString); return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }); },

        resetForm() {
          this.form = { email: '', contact: '', first_name: '', middle_name: '', last_name: '', birthdate: '', is_pwd: 'no', pwd_id: '', senior_id: '', service_type: 'Tax Declaration' };
          this.showingModal = false;
          this.thankYouModal = false;
          this.showQueueModal = false;
          this.isEmailValid = true;
          this.isContactValid = true;
          this.isAgeValid = true;
          this.isSenior = false;
          this.calculatedAge = null;
          this.queueNumber = null;
          this.applicationId = null;
          this.isPriority = false;
          this.priorityType = '';
        },

        get isFormValid() {
          return this.isEmailValid && this.isContactValid && this.isAgeValid &&
                 this.form.email && this.form.contact && this.form.first_name &&
                 this.form.last_name && this.form.birthdate;
        },

        showModal() {
          if (!this.isFormValid) {
            alert(this.translations[this.selectedLanguage][!this.isAgeValid ? 'ageError' : 'Please fill all required fields correctly.']);
            return;
          }
          this.showingModal = true;
        },

        async submitForm() {
          this.showingModal = false;
          try {
            const res = await fetch("/queue/kiosk", {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              },
              body: JSON.stringify(this.form),
            });

            const contentType = res.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
              alert('Server error: Invalid response from server.');
              return;
            }

            const data = await res.json();

            if (data.success) {
              this.queueNumber = data.queue_number;
              this.isPriority = data.is_priority;
              this.priorityType = data.priority_type || 'Regular';
              this.applicationId = data.application_id;
              this.showQueueModal = true;

              // ✅ Auto-print ticket
              this.printQueueTicket();

              this.resetIdleTimer();
            } else {
              alert(data.message || 'Submission failed');
            }
          } catch (err) {
            console.error('Submission error:', err);
            alert('Failed to connect. Please try again.');
          }
        },

        // ✅ Auto-print ticket after submission
        printQueueTicket() {
          if (!this.applicationId) return;

          setTimeout(() => {
            const printUrl = `/user/online/queue-ticket/${this.applicationId}`;
            const printWindow = window.open(printUrl, 'PrintTicket', 'width=350,height=400');
          }, 800);
        }
      };
    }
  </script>
</body>
</html>