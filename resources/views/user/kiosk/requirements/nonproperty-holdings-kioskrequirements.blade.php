<!DOCTYPE html>
<html lang="en" x-data="requirementFormHandler()" x-init="init()">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Non-Property Holdings Requirements</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/alpinejs" defer></script>
</head>
<body class="m-0 p-0 bg-gray-100">

  <!-- Background Section -->
  <section class="relative h-screen bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('img/bgbackground2.jpg') }}')">
    <div class="absolute inset-0 bg-gray-700/60 z-0"></div>

    <div class="relative z-10 flex items-center justify-center h-full px-4">
      <div class="w-full max-w-4xl h-[90vh] bg-[#f5f5f4] border border-white rounded-2xl p-6 shadow-xl flex flex-col overflow-hidden">

        <!-- Instructions -->
        <div class="mb-4">
          <h1 class="text-xl md:text-2xl font-bold text-black font-serif mb-1">Required Documents</h1>
          <p class="text-sm text-gray-800">Please make sure all of the following documents are ready before proceeding.</p>
        </div>

        <!-- Checklist Form -->
        <form class="flex flex-col flex-1 min-h-0">
          <div class="flex-1 min-h-0 overflow-y-auto pr-2 space-y-3 text-black text-base leading-relaxed">

            <!-- Checklist -->
            <template x-for="(item, index) in requirements" :key="index">
              <label class="flex items-start gap-3 bg-white p-3 rounded-md shadow-sm hover:bg-gray-100 transition">
                <input type="checkbox" class="mt-1 w-5 h-5 text-amber-600 border-gray-300 rounded" x-model="checked[index]">
                <span class="text-gray-900" x-text="item"></span>
              </label>
            </template>

            <!-- Checklist Counter -->
            <div class="pt-3 text-sm text-gray-700 font-medium">
              <span x-text="`${checkedCount} of ${requirements.length} items checked`"></span>

              <!-- Progress Bar -->
              <div class="w-full h-3 mt-2 bg-gray-300 rounded-full overflow-hidden">
                <div class="h-full bg-amber-600 transition-all duration-300" :style="{ width: progressPercent + '%' }"></div>
              </div>
            </div>
          </div>

          <!-- Proceed Button -->
          <div class="pt-4 border-t border-gray-300 mt-4">
            <button
              type="button"
              :disabled="!canProceed"
              @click="openReminder()"
              :class="canProceed
                ? 'bg-amber-600 hover:bg-amber-700 cursor-pointer'
                : 'bg-amber-400 cursor-not-allowed opacity-60'"
              class="w-full px-6 py-3 text-lg font-semibold rounded-md text-white transition-all duration-300"
            >
              Proceed
            </button>
          </div>
        </form>
      </div>
    </div>
  </section>

  <!-- Reminder Modal -->
  <div x-show="showReminder" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center px-4" x-cloak>
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-auto p-6 text-center space-y-4 animate-fade-in">
      <div class="space-y-1">
        <svg class="mx-auto w-10 h-10 text-red-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9 9 4.03 9 9z" />
        </svg>
        <h2 class="text-xl font-bold text-red-700">Reminder</h2>
      </div>
      <p class="text-gray-700 text-sm leading-relaxed">
        Make sure you have <strong>all documents</strong> ready. You will need to bring these to City Hall to proceed with your request.
      </p>
      <div class="flex justify-center gap-4 pt-2">
        <button @click="showReminder = false" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded shadow-sm">Cancel</button>
        <button @click="confirmProceed()" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded shadow-sm">I Understand</button>
      </div>
    </div>
  </div>

    <!-- AlpineJS Logic -->
  <script>
    function requirementFormHandler() {
      const urlParams = new URLSearchParams(window.location.search);
      const serviceType = urlParams.get('service_type') || 'Unknown';

      return {
        requirements: [
          'KIOSK',
        ],
        checked: [],
        showReminder: false,
        serviceType: serviceType,

        init() {
          this.checked = new Array(this.requirements.length).fill(false);
        },

        get canProceed() {
          return this.checked.every(Boolean);
        },

        get checkedCount() {
          return this.checked.filter(Boolean).length;
        },

        get progressPercent() {
          return (this.checkedCount / this.requirements.length) * 100;
        },

        openReminder() {
          this.showReminder = true;
        },

        confirmProceed() {
          this.showReminder = false;

          // DEBUG: Log current URL and detection
          console.log('Current URL:', window.location.pathname);
          const isKiosk = window.location.pathname.includes('/kiosk/');
          console.log('Is Kiosk Mode?', isKiosk);
          console.log('Service Type:', this.serviceType);

          let targetPath = '';

          if (isKiosk) {
            console.log('ROUTING TO KIOSK FORMS');
            // Kiosk routing
            switch (this.serviceType.toLowerCase()) {
              case 'tax declaration':
                targetPath = '/kiosk/forms/tax-declaration';
                break;
              case 'certificate of no improvement':
                targetPath = '/kiosk/forms/no-improvement';
                break;
              case 'certificate of property holdings':
                targetPath = '/kiosk/forms/property-holdings';
                break;
              case 'certificate of non-property holdings':
                targetPath = '/kiosk/forms/non-property-holdings';
                break;
              default:
                targetPath = '/kiosk/forms/tax-declaration';
            }
        
          }

          console.log('Target Path:', targetPath);
          console.log('Full URL:', `${targetPath}?service_type=${encodeURIComponent(this.serviceType)}`);

          window.location.href = `${targetPath}?service_type=${encodeURIComponent(this.serviceType)}`;
        }
      }
    }
  </script>

  <style>
    [x-cloak] { display: none !important; }
    .animate-fade-in {
      animation: fadeIn 0.3s ease-out forwards;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</body>
</html>