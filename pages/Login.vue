<template>
  <nb-container>
    <nb-content :style="{ marginTop: 80 }">
      <view
        :style="{ flex: 1, justifyContent: 'center', alignItems: 'center' }"
      >
        <image :source="require('../assets/images/logo.png')" />
      </view>
      <nb-form
        v-if="!registration"
        :style="{ paddingLeft: 20, paddingRight: 20 }"
      >
        <nb-item floatingLabel>
          <nb-label>{{ $root.lang.t('email') }}</nb-label>
          <nb-input v-model="email" />
        </nb-item>
        <nb-item floatingLabel last>
          <nb-label>{{ $root.lang.t('password') }}</nb-label>
          <nb-input v-model="password" secure-text-entry />
        </nb-item>
        <nb-button
          class="btn"
          block
          info
          :style="{ marginTop: 20 }"
          :on-press="login"
        >
          <nb-spinner v-if="logging_in" size="small" />
          <nb-text>{{ $root.lang.t('login') }}</nb-text>
        </nb-button>
        <nb-button
          block
          transparent
          :style="{ marginTop: 20 }"
          :on-press="toRegister"
        >
          <nb-text>{{ $root.lang.t('register') }}</nb-text>
        </nb-button>
      </nb-form>
      <nb-form
        v-if="registration"
        :style="{ paddingLeft: 20, paddingRight: 20 }"
      >
        <nb-item floatingLabel>
          <nb-label>{{ $root.lang.t('email') }}</nb-label>
          <nb-input />
        </nb-item>
        <nb-item floatingLabel>
          <nb-label>{{ $root.lang.t('name') }}</nb-label>
          <nb-input />
        </nb-item>
        <nb-item floatingLabel>
          <nb-label>{{ $root.lang.t('password') }}</nb-label>
          <nb-input secure-text-entry />
        </nb-item>
        <nb-item floatingLabel last>
          <nb-label>{{ $root.lang.t('confirm_password') }}</nb-label>
          <nb-input secure-text-entry />
        </nb-item>
        <nb-button
          class="btn"
          block
          success
          :style="{ marginTop: 20 }"
          :on-press="register"
        >
          <nb-text>{{ $root.lang.t('register') }}</nb-text>
        </nb-button>
        <nb-button
          block
          transparent
          :style="{ marginTop: 20 }"
          :on-press="backToLogin"
        >
          <nb-text>{{ $root.lang.t('login') }}</nb-text>
        </nb-button>
      </nb-form>
    </nb-content>
  </nb-container>
</template>

<script>
import { AsyncStorage } from 'react-native';
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
      },
    };
  },
  computed: {
    logging_in() {
      // return loggedIn = true;
    },
  },
  created() {},
  methods: {
    login: async function () {
      try {
        let response = await fetch('http://api.arsus.nl/client', {
          method: 'POST',
          headers: {
            Accespt: 'application/json',
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
          };

          AsyncStorage.setItem('login', JSON.stringify(this.user), () => {
            AsyncStorage.mergeItem(
              'login',
              JSON.stringify(user_updated),
              () => {
                AsyncStorage.getItem('login', (err, result) => {
                  console.log(result);
                });
              }
            );
          });

          AsyncStorage.getItem('login').then((val) => {
            if (val) {
              console.log(val);
              this.$root.loggedIn = true;
            } else {
              return false;
            }
          });
        } else {
          alert(JSON.stringify(responseJson));
        }
      } catch (error) {
        alert(error);
        console.error(error);
      }
    },
    toRegister: function () {
      this.registration = true;
    },
    backToLogin: function () {
      this.registration = false;
    },
    register: function () {
      //
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
