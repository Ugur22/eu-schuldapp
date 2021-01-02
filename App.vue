<template>
  <nb-container>
      <user-start v-if="!loggedIn && isAppReady"></user-start>
      <root v-if="loggedIn">
          <app-loading v-if="!isAppReady"> </app-loading>
          <app-navigator v-if="isAppReady"></app-navigator>
      </root>
  </nb-container>
</template>

<script>
  import Vue from "vue-native-core";
  import { Ionicons } from "@expo/vector-icons";
  import { Root, VueNativeBase } from "native-base";
  import * as Font from 'expo-font';
  import * as Localization from 'expo-localization';
  import i18n from 'i18n-js';
   import { AppLoading } from "expo";

  i18n.translations = {
    en: require('./locales/en.json'),
    nl: require('./locales/nl.json'),
    tr: require('./locales/tr.json'),
  };
  i18n.locale = 'nl';
  i18n.fallbacks = true;

  Vue.component("ionicons", Ionicons);
  Vue.use(VueNativeBase);

  import {
    createAppContainer,
    createStackNavigator,
  } from "vue-native-router";

  import UserStart from "./pages/Login.vue";
  import HomeScreen from "./pages/client/Home.vue";
  import AccountScreen from "./pages/client/Account.vue";
  import AppointmentsScreen from "./pages/client/Appointments.vue";
  import AppointmentScreen from "./pages/consultant/Appointment";
  import DateScreen from "./pages/consultant/MakeAppointment";
  import DocumentsScreen from "./pages/client/Documents.vue";
  import DebtsScreen from "./pages/client/Debts.vue";
  import DebtDetailScreen from "./pages/client/DebtDetails.vue";
  import FormsScreen from "./pages/client/Forms.vue";
  import CollectorDocsScreen from "./pages/client/Collector.vue";
  import OtherDocsScreen from "./pages/client/OtherDocs.vue";
  import HelpScreen from "./pages/client/Help.vue";
  import ConsultantScreen from "./pages/consultant/Consultant";
  import ClientsScreen from "./pages/consultant/Clients";
  import AppointmentsConsultantScreen from "./pages/consultant/AppointmentsConsultant";
  import FileClientScreen from "./pages/consultant/FileClient";
  import DebtClientScreen from "./pages/consultant/Debts";
  import DebtClientDetailScreen from "./pages/consultant/DebtDetailsClient";
  import AppointmentConfirmationtScreen from "./pages/consultant/AppointmentConfirmation";
  import OtherDocsConsultantScreen from "./pages/consultant/OtherDocsConsultant";
  import FormsConsultantScreen from "./pages/consultant/FormsConsultant";
  import CollectorConsultantScreen from "./pages/consultant/CollectorConsultant";
  import OtherDocsDetailsScreen from "./pages/consultant/OtherDocsDetails";

  let PageStart;

  const StackNavigator = createStackNavigator(
    {
      Home: {
        screen: HomeScreen
      },
      Account: {
        screen: AccountScreen
      },
      Appointments: {
        screen: AppointmentsScreen
      },
      Documents: {
        screen: DocumentsScreen
      },
      DebtList: {
        screen: DebtsScreen
      },
      DebtDetails: {
        screen: DebtDetailScreen
      },
      FormList: {
        screen: FormsScreen
      },
      DocsCollector: {
        screen: CollectorDocsScreen
      },
      DocsOthers: {
        screen: OtherDocsScreen
      },
      Consultant: {
        screen: ConsultantScreen
      },
      Clients: {
        screen: ClientsScreen
      },
      AppointmentsConsultant: {
        screen: AppointmentsConsultantScreen
      },
      FileClient: {
        screen: FileClientScreen
      },
      Debts: {
        screen: DebtClientScreen
      },
      DebtDetailsClient: {
        screen: DebtClientDetailScreen
      },
      Appointment: {
        screen: AppointmentScreen
      },
      MakeAppointment: {
        screen: DateScreen
      },
      AppointmentConfirmation: {
        screen: AppointmentConfirmationtScreen
      },
      OtherDocsConsultant: {
        screen: OtherDocsConsultantScreen
      },
      FormsConsultant: {
        screen: FormsConsultantScreen
      },
      CollectorConsultant: {
        screen: CollectorConsultantScreen
      },
      OtherDocsDetails: {
        screen: OtherDocsDetailsScreen
      },
    },
    {
      initialRouteName: 'Home',
      headerMode: 'none'
    }
  );

  const AppNavigator = createAppContainer(StackNavigator);

  export default { 
    data() {
      return {
        lang: i18n,
        loggedIn: false,
        isAppReady: false
      };
    },
    components: { AppLoading, Root, AppNavigator, Ionicons, UserStart },
    created() {
      this.loadFonts();
    },
    methods: {
       loadFonts: async function () {
        try {
          this.isAppReady = false;
          await Font.loadAsync({
            Roboto: require("./node_modules/native-base/Fonts/Roboto.ttf"),
            Roboto_medium: require("./node_modules/native-base/Fonts/Roboto_medium.ttf")
          });
          this.isAppReady = true;
        } catch (error) {
          console.log("some error occured", error);
        }
      },
    }
  }
</script>

<style>
.container {
  background-color: white;
  align-items: center;
  justify-content: center;
  flex: 1;
}
.text-color-primary {
  color: #0078ae;
}
.btn,
.text {
  background-color: #0078ae;
}
</style>
