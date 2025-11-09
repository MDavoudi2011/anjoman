
export interface User {
  username: string;
  password?: string; // Only used for reading from users.json, not stored in state.
  group: 'A' | 'B';
  watchedVideos: string[];
}

export interface Video {
  id: string;
  title: string;
  duration: string;
  thumbnailUrl: string;
}

export interface VideoCategory {
  categoryTitle: string;
  videos: Video[];
}

export interface PracticeChallenge {
  id: string;
  title: string;
  difficulty: 'آسان' | 'متوسط' | 'سخت';
  description: string;
}

export interface TestData {
    title: string;
    active: boolean;
    message: string;
}

export enum View {
  Training = 'TRAINING',
  Practice = 'PRACTICE',
  Test = 'TEST',
  Profile = 'PROFILE',
}
