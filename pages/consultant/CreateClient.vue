<template>
  <nb-container>
    <nb-content :style="{ marginTop: 80 }">
      <nb-form :style="{ paddingLeft: 20, paddingRight: 20 }">
        <nb-item floatingLabel>
          <nb-label>{{ $root.lang.t('initials') }}</nb-label>
          <nb-input v-model="initials" />
        </nb-item>
        <nb-item floatingLabel>
          <nb-label>{{ $root.lang.t('firstname') }}</nb-label>
          <nb-input v-model="firstname" />
        </nb-item>
        <nb-item floatingLabel>
          <nb-label>{{ $root.lang.t('lastname') }}</nb-label>
          <nb-input v-model="lastname" />
        </nb-item>
        <view>
          <nb-date-picker
            :minimumDate="minimumDate"
            :modalTransparent="false"
            animationType="fade"
            androidMode="default"
            placeHolderText="Select birthdate"
            :textStyle="{ color: '#0078ae' }"
            :placeHolderTextStyle="{ color: '#d3d3d3' }"
            :onDateChange="setDate"
          />
          <nb-text>
            {{ chosenDate.toString().substr(4, 12) }}
            {{ chosenDate.toString().substr(4, 12) }}
          </nb-text>
        </view>
        <nb-card-item floatingLabel>
          <nb-label>{{ $root.lang.t('gender') }}</nb-label>
          <nb-picker
            mode="dialog"
            placeholder="gender"
            :selectedValue="selectedGender"
            :onValueChange="onGenderChange"
          >
            <item label="male" value="male" />
            <item label="female" value="female" />
          </nb-picker>
        </nb-card-item>
        <nb-item floatingLabel>
          <nb-label>{{ $root.lang.t('adres') }}</nb-label>
          <nb-input v-model="adres" />
        </nb-item>
        <nb-card-item floatingLabel>
          <nb-label>{{ $root.lang.t('Township') }}</nb-label>
          <nb-picker
            mode="dialog"
            placeholder="gender"
            :selectedValue="selectedLocation"
            :onValueChange="onLocationChange"
          >
            <item
              v-for="location in locations"
              :key="location.id"
              :label="location.name"
              :value="location.id"
            />
          </nb-picker>
        </nb-card-item>
        <nb-item floatingLabel>
          <nb-label>{{ $root.lang.t('BSN') }}</nb-label>
          <nb-input v-model="bsn" />
        </nb-item>
        <nb-item floatingLabel>
          <nb-label>{{ $root.lang.t('email') }}</nb-label>
          <nb-input v-model="emailRegister" />
        </nb-item>
        <nb-item floatingLabel>
          <nb-label>{{ $root.lang.t('password') }}</nb-label>
          <nb-input v-model="passwordRegister" secure-text-entry />
        </nb-item>
        <nb-item floatingLabel last>
          <nb-label>{{ $root.lang.t('confirm_password') }}</nb-label>
          <nb-input v-model="passwordRepeat" secure-text-entry />
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
import { required, email } from 'vuelidate/lib/validators';
import { Picker } from 'native-base';

export default {
  data() {
    return {
      loaded: false,
      date: this.addDays(1),
      minimumDate: this.addDays(1),
      chosenDate: new Date(),
      days: [
        'zondag',
        'maandag',
        'dinsdag',
        'woensdag',
        'donderdag',
        'vrijdag',
        'zaterdag',
      ],
      selectedGender: 'male',
      selectedLocation: '0',
      locations: {},
      dataIsReady: false,
      initials: '',
      firstname: '',
      lastname: '',
      adres: '',
      bsn: '',
      emailRegister: '',
      passwordRegister: '',
      passwordRepeat: '',
    };
  },
  created() {
    this.getLocations();
  },

  components: { Item: Picker.Item },
  methods: {
    setDate: function (newDate) {
      this.chosenDate = newDate;
    },
    addDays: function (days) {
      let date = new Date();
      date.setDate(date.getDate() + days);
      return date;
    },
    onGenderChange: function (value) {
      this.selectedGender = value;
    },
    onLocationChange: function (value) {
      this.selectedLocation = value;
    },
    getLocations: async function () {
      try {
        let response = await fetch('http://api.arsus.nl/client/locations', {
          method: 'POST',
          headers: {
            accept: 'application/json',
            'Content-Type': 'application/json',
          },
        });

        let responseJson = await response.json();
        if (responseJson.success) {
          this.locations = responseJson.results;
          this.dataIsReady = true;
        }
      } catch (error) {
        console.log(error);
        console.error(error);
      }
    },
    register: async function () {
      let formatDate =
        this.chosenDate.getFullYear() +
        '-' +
        (this.chosenDate.getMonth() + 1) +
        '-' +
        this.chosenDate.getDate();

      try {
        let response = await fetch('http://api.arsus.nl/auth/register', {
          method: 'POST',
          headers: {
            accept: 'application/json',
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            email: this.emailRegister,
            password: this.passwordRegister,
            birth_date: formatDate,
            place_id: this.selectedLocation,
            address: this.adres,
            gender: this.selectedGender,
            initial: this.initials,
            firstname: this.firstname,
            lastname: this.lastname,
            card_id: this.bsn,
            confirm_password: this.passwordRepeat,
          }),
        });

        let responseJson = await response.json();
        if (responseJson.success) {
          console.log('client added');
        } else {
          console.log(responseJson);
        }
      } catch (error) {
        console.log(error);
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
