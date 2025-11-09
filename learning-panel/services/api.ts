
import type { User, VideoCategory, PracticeChallenge, TestData } from '../types';

// This is a MOCK API layer. In a real application, these would be
// network requests to a backend server (e.g., using fetch to a REST API).
// Here, we simulate it by fetching local JSON files.

export const api = {
  login: async (username: string, password: string): Promise<User | null> => {
    try {
      const response = await fetch('/data/users.json');
      if (!response.ok) throw new Error('Network response was not ok');
      const users: (User & { password?: string })[] = await response.json();
      
      const user = users.find(u => u.username === username && u.password === password);

      if (user) {
        // In a real app, you wouldn't send the password back to the client.
        delete user.password;
        return user as User;
      }
      return null;
    } catch (error) {
      console.error("Failed to login:", error);
      return null;
    }
  },

  getTrainingContent: async (group: 'A' | 'B'): Promise<VideoCategory[]> => {
    try {
      const response = await fetch('/data/videos.json');
      if (!response.ok) throw new Error('Network response was not ok');
      const allContent = await response.json();
      return allContent[group] || [];
    } catch (error) {
      console.error("Failed to fetch training content:", error);
      return [];
    }
  },

  getPracticeContent: async (group: 'A' | 'B'): Promise<PracticeChallenge[]> => {
    try {
        const response = await fetch('/data/exercises.json');
        if (!response.ok) throw new Error('Network response was not ok');
        const allContent = await response.json();
        return allContent[group] || [];
      } catch (error) {
        console.error("Failed to fetch practice content:", error);
        return [];
      }
  },

  getTestData: async (group: 'A' | 'B'): Promise<TestData | null> => {
    try {
        const response = await fetch('/data/tests.json');
        if (!response.ok) throw new Error('Network response was not ok');
        const allContent = await response.json();
        return allContent[group] || null;
      } catch (error) {
        console.error("Failed to fetch test data:", error);
        return null;
      }
  },

  // In a real backend, this function would make a PUT/POST request to save data.
  // Here, we just log it to show what would be sent. The state is managed
  // in the App component and is lost on refresh, unlike a real backend.
  updateUserProgress: async (user: User): Promise<boolean> => {
    console.log("SIMULATING API CALL TO SAVE USER PROGRESS");
    console.log("In a real app, this data would be sent to the backend:", user);
    console.log("To make this data persist, you would need a server-side script (e.g., in PHP, Node.js, Python) to read this data and write it back to the users.json file.");
    // Simulate a successful API call
    return Promise.resolve(true);
  },
};
