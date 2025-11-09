
import React, { useState, useEffect } from 'react';
import type { User, VideoCategory, Video } from '../types';
import { api } from '../services/api';
import PlayIcon from './icons/PlayIcon';
import VideoPlayerModal from './VideoPlayerModal';

interface TrainingViewProps {
  user: User;
  updateUser: (user: User) => void;
}

const TrainingView: React.FC<TrainingViewProps> = ({ user, updateUser }) => {
  const [content, setContent] = useState<VideoCategory[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [playingVideo, setPlayingVideo] = useState<Video | null>(null);

  useEffect(() => {
    const fetchContent = async () => {
      setIsLoading(true);
      const data = await api.getTrainingContent(user.group);
      setContent(data);
      setIsLoading(false);
    };
    fetchContent();
  }, [user.group]);

  const handleVideoWatched = (videoId: string) => {
    if (!user.watchedVideos.includes(videoId)) {
      const newWatchedVideos = [...user.watchedVideos, videoId];
      updateUser({ ...user, watchedVideos: newWatchedVideos });
    }
  };

  if (isLoading) {
    return <div className="text-center">در حال بارگذاری دوره‌ها...</div>;
  }

  return (
    <div className="space-y-8">
      <h2 className="pb-2 text-3xl font-extrabold text-gray-800 border-b-4 border-indigo-500 dark:text-white">دوره‌های آموزشی</h2>
      {content.map((category) => (
        <section key={category.categoryTitle}>
          <h3 className="mb-4 text-2xl font-bold text-gray-700 dark:text-gray-300">{category.categoryTitle}</h3>
          <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            {category.videos.map((video) => {
              const isWatched = user.watchedVideos.includes(video.id);
              return (
                <div
                  key={video.id}
                  className="overflow-hidden bg-white rounded-lg shadow-lg cursor-pointer group dark:bg-gray-800"
                  onClick={() => setPlayingVideo(video)}
                >
                  <div className="relative">
                    <img src={video.thumbnailUrl} alt={video.title} className="object-cover w-full h-40 transition-transform duration-300 group-hover:scale-105" />
                    <div className="absolute inset-0 flex items-center justify-center bg-black bg-opacity-40">
                      <PlayIcon />
                    </div>
                    {isWatched && (
                       <div className="absolute px-2 py-1 text-xs font-bold text-white bg-green-600 rounded-full top-2 right-2">تکمیل شد</div>
                    )}
                  </div>
                  <div className="p-4">
                    <h4 className="font-semibold text-gray-800 dark:text-white">{video.title}</h4>
                    <p className="text-sm text-gray-500 dark:text-gray-400">مدت: {video.duration}</p>
                  </div>
                </div>
              );
            })}
          </div>
        </section>
      ))}
      {playingVideo && (
        <VideoPlayerModal 
            video={playingVideo}
            onClose={() => setPlayingVideo(null)}
            onWatched={handleVideoWatched}
        />
      )}
    </div>
  );
};

export default TrainingView;
