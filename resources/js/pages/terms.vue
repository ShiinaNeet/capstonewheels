<template>
  <div>
    <navigation />
    <div class="terms-container mx-auto max-w-2xl p-6">
      <h2 class="text-2xl font-bold mb-4">Terms and Conditions for Online Enrollment System</h2>

      <template v-if="companyInfo[0] && companyInfo[0].terms">
        <template v-for="section in companyInfo[0].terms.split('\n\n')" :key="section" class="mb-4 md:mb-6">
          <template v-if="section.trim() !== ''">
            <h3 class="text-lg md:text-xl font-bold mb-2">{{ section.split('\n')[0] }}</h3>
            <p class="text-sm md:text-base">
              {{ section.split('\n').slice(1).join('\n') }}
            </p>
          </template>
        </template>
      </template>

      <template v-else>
        <p>No terms available</p>
      </template>
    </div>
  </div>
</template>

<script>
import navmenu from '@/components/navbar.vue';

export default {
    data() {
      return {
        companyInfo: [],
      };
    },
    mounted() {
      this.getCompanyInfo();
    },
    methods: {
      getCompanyInfo() {
        axios({
          method: 'GET',
          type: 'JSON',
          url: '/company',
        })
          .then((response) => {
            if (response.data.status == 1) {
              this.companyInfo = response.data.result;
            } else this.$root.prompt(response.data.text);
          })
          .catch((error) => {
            this.$root.prompt(error.response.data.message);
          });
      },
    },
    components: {
       navigation: navmenu
    }
};
</script>

