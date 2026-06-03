import { useDashboardStore } from "../stores/dashboard";
import { useCalendarStore } from "../stores/calendar";
import { useVolunteersStore } from "../stores/volunteers";
import { useAboutStore } from "../stores/about";

/** Fetch API data when entering a route. */
export async function loadRouteData(routeName) {
  switch (routeName) {
    case "dashboard":
      return useDashboardStore().fetchDashboard();
    case "calendar":
      return useCalendarStore().fetchEvents();
    case "volunteers":
      return useVolunteersStore().fetchVolunteers();
    case "about":
      return useAboutStore().fetchAbout();
    default:
      return undefined;
  }
}
