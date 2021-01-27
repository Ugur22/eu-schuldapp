import moment from "moment";

export function formatDate(date) {
    let Formatdate = moment(date).format("DD-MM-YYYY");
    return Formatdate;
	}
	
	export function formatDateReverse(date) {
    let Formatdate = moment(date).format("YYYY-MM-DD");
    return Formatdate;
  }

  export function FormatTime(date) {
    let FormatTime = moment(date).format("HH:mm");
    return FormatTime;
  }
  
  export function formatDay (date) {
    let formatDay = moment(date).format("dddd");
    return formatDay;
  }

  