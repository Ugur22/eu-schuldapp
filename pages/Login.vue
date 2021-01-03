<template>
  <nb-container>
    <nb-content :style="{ marginTop: 80 }">
      <view :style="{ flex: 1, justifyContent: 'center', alignItems: 'center' }">
        <image :source="require('../assets/images/logo.png')" />
      </view>
      <nb-form :style="{ paddingLeft: 20, paddingRight: 20 }">
        <nb-item floatingLabel>
          <nb-label>{{ $root.lang.t('email') }}</nb-label>
          <nb-input v-model="email" />
        </nb-item>
        <nb-item floatingLabel last>
          <nb-label>{{ $root.lang.t('password') }}</nb-label>
          <nb-input v-model="password" secure-text-entry />
        </nb-item>
        <nb-button class="btn" block info :style="{ marginTop: 20 }" :on-press="login">
          <!-- <nb-spinner v-if="$root.loggedIn" color="#0078ae" /> -->
          <nb-text>{{ $root.lang.t('login') }}</nb-text>
        </nb-button>
      </nb-form>
    </nb-content>
  </nb-container>
</template>

<script>
import AsyncStorage from '@react-native-async-storage/async-storage';
import { required, email } from 'vuelidate/lib/validators';

export default {
  data() {
    return {
      registration: false,
      loaded: false,
      email: '',
      password: '',
      user: {
        email: '',
        password: '',
        type:''
      },
      dataIsReady: false,
    };
  },
  computed: {
    logging_in() {
      // return loggedIn = true;
    },
  },

  components: {},
  methods: {
    login: async function () {
      try {
        let response = await fetch('http://api.arsus.nl/login', {
          method: 'POST',
          headers: {
            accept: 'application/json',
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            email: this.email,
            password: this.password,
          }),
        });

        let responseJson = await response.json();
        if (responseJson.success) {
          let user_updated = {
            email: this.email,
            password: this.password,
            type:responseJson.user.role.slug,
            token:responseJson.token.token
          };

          AsyncStorage.setItem('login', JSON.stringify(this.user), () => {
            AsyncStorage.mergeItem('login', JSON.stringify(user_updated));
          });

          this.$root.loggedIn = true; 

        } else {
          alert(JSON.stringify(responseJson));
        }
      } catch (error) {
         AsyncStorage.removeItem('login');
        alert(error);
        console.error(error);
      }
    },
  },
};
</script>
<style>
.btn,
.text {
  background-color: #0078ae;
}
</style>
