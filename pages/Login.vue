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
import { LogBox,Alert } from "react-native";

export default {
  data() {
    return {
      registration: false,
      loaded: false,
      email: '',
      password: '',
			dataIsReady: false,
			user: {
				email: '',
				password: '',
				type:''
		},
    };
  },
  computed: {
    logging_in() {
      // return loggedIn = true;
		},
		created() {
			LogBox.ignoreLogs([
				'DatePickerIOS has been merged with DatePickerAndroid and will be removed in a future release.',
				'StatusBarIOS has been merged with StatusBar and will be removed in a future release.',
				'DatePickerAndroid has been merged with DatePickerIOS and will be removed in a future release.'
			]);
		}
  },

  components: {},
  methods: {
    login: async function () {
			let that = this;
      try {
        let response = await fetch('http://api.arsus.nl/login', {
          method: 'POST',
          headers: {
            accept: 'application/json',
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            email: this.email.replace(/\s/g, ''),
						password: this.password.replace(/\s/g, ''),
          }),
        });

        let responseJson = await response.json();
        if (responseJson.success) {
          let nameUser = ''
          if(responseJson.user.client){
            nameUser = responseJson.user.client.firstname + ' ' + responseJson.user.client.lastname;
          }

          if(responseJson.user.consultant){
            nameUser = responseJson.user.consultant.firstname + ' ' + responseJson.user.consultant.lastname;
          }
          let user_updated = {
            email: this.email,
            password: this.password,
            type:responseJson.user.role.slug,
            token:responseJson.token.token,
            name:nameUser
          };

          AsyncStorage.setItem('login', JSON.stringify(that.user), () => {
            AsyncStorage.mergeItem('login', JSON.stringify(user_updated));
					});
					

				this.$root.user = user_updated;
          this.$root.loggedIn = true; 

        } else {
					Alert.alert(
						`${this.$root.lang.t('login_failed')}`,
						`${this.$root.lang.t('login_failed_message')}`,
						[
							{
								text: 'ok',
								style: 'cancel'
							}
						],
						{ cancelable: false }
					);
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
