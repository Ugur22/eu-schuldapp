<template>
  <nb-container>
    <nb-header :style="{ backgroundColor: '#0078ae' }">
      <nb-left>
        <nb-button transparent>
          <nb-icon name="arrow-back" :on-press="goBack" />
        </nb-button>
      </nb-left>
      <nb-body>
        <nb-title>{{ $root.lang.t('debts') }}</nb-title>
      </nb-body>
      <nb-right>
        <nb-button transparent>
          <nb-icon name="information-circle" />
        </nb-button>
      </nb-right>
    </nb-header>
    <nb-content>
      <nb-item :style="{ borderColor: '#62B1F6' }">
        <nb-input placeholder="Search" />
      </nb-item>
      <nb-list>
        <nb-list-item v-for="debt in clientDebts" :key="clientDebts.ID">
          <nb-left>
            <nb-button transparent :on-press="() => detailDebt(1)">
              <nb-text>{{debt.Incasseerder}}</nb-text>
            </nb-button>
          </nb-left>
          <nb-body>
            <nb-text>{{debt.schuld}}</nb-text>
          </nb-body>
          <nb-right>
            <nb-icon name="arrow-forward" />
          </nb-right>
        </nb-list-item>
      </nb-list>
    </nb-content>
    <nb-footer>
      <footer-nav
        :style="{ backgroundColor: '#0078ae' }"
        activeBtn="docs"
      ></footer-nav>
    </nb-footer>
    <modal v-if="isModalVisible">
      <nb-header>
        <nb-left>
          <nb-button transparent>
            <nb-icon name="arrow-back" :on-press="dismissModal" />
          </nb-button>
        </nb-left>
        <nb-body>
          <nb-title>Details</nb-title>
        </nb-body>
        <nb-right />
      </nb-header>
      <nb-content>
        <nb-card
          :style="{
            marginLeft: 20,
            marginRight: 20,
            marginTop: 20,
            marginBottom: 20,
          }"
        >
          <nb-card-item rounded :style="{ backgroundColor: '#62B1F6' }">
            <nb-body>
              <nb-grid class="marginBottom">
                <nb-col>
                  <nb-text class="headerText">Kenmerk Ref</nb-text>
                  <nb-text class="detailText">12345678</nb-text>
                </nb-col>
                <nb-col>
                  <nb-text class="headerText">Percentage</nb-text>
                  <nb-text class="detailText">12345678</nb-text>
                </nb-col>
              </nb-grid>
              <nb-grid class="marginBottom">
                <nb-col>
                  <nb-text class="headerText">Incasseerder</nb-text>
                  <nb-text class="detailText">12345678</nb-text>
                </nb-col>
                <nb-col>
                  <nb-text class="headerText">Schuld</nb-text>
                  <nb-text class="detailText">$585.89</nb-text>
                </nb-col>
              </nb-grid>
            </nb-body>
          </nb-card-item>
        </nb-card>
      </nb-content>
    </modal>
  </nb-container>
</template>

<style>
.headerText {
  color: white;
  font-weight: bold;
}
.detailText {
  color: white;
}
.marginBottom {
  margin-bottom: 20px;
}
</style>

<script>
import Modal from 'react-native-modal';
import FooterNav from '../included/Footer';
import { AsyncStorage } from 'react-native';

export default {
  props: {
    navigation: {
      type: Object,
      user: {},
    },
  },
  components: { FooterNav },
  data() {
    return {
      isModalVisible: false,
      clientDebts: {},
    };
  },
  created() {
    this.userData();
  },
  methods: {
    userData: async function () {
      let value = '';
      try {
        value = await AsyncStorage.getItem('login');
        this.user = JSON.parse(value);
      } catch (error) {
        // Error retrieving data
        console.log(error.message);
      }

      try {
        let response = await fetch('http://api.arsus.nl/client/docs/debts', {
          method: 'POST',
          headers: {
            Accespt: 'application/json',
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            email: this.user.email,
            password: this.user.password,
          }),
        });

        let responseJson = await response.json();
        if (responseJson.success) {
          this.clientDebts = responseJson.results;
        } else {
          console.log(responseJson);
        }
      } catch (error) {
        console.log(error);
        console.error(error);
      }
    },
    goBack: function () {
      this.navigation.goBack();
    },
    goToPage: function (page) {
      this.navigation.navigate(page);
    },
    detailDebt: function (id) {
      this.isModalVisible = true;
    },
    dismissModal: function () {
      this.isModalVisible = false;
    },
  },
};
</script>
