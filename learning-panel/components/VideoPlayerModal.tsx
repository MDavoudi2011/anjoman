
import React, { useState, useEffect } from 'react';
import type { Video } from '../types';

interface VideoPlayerModalProps {
  video: Video;
  onClose: () => void;
  onWatched: (videoId: string) => void;
}

const VideoPlayerModal: React.FC<VideoPlayerModalProps> = ({ video, onClose, onWatched }) => {
  const [progress, setProgress] = useState(0);

  useEffect(() => {
    // Simulate video playback over 4 seconds
    const interval = setInterval(() => {
      setProgress(prev => {
        if (prev >= 100) {
          clearInterval(interval);
          onWatched(video.id);
          onClose(); // Close modal after watching
          return 100;
        }
        return prev + 1;
      });
    }, 40); // 40ms * 100 steps = 4000ms = 4 seconds

    return () => clearInterval(interval);
  }, [video.id, onWatched, onClose]);

  return (
    <div
      className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-70"
      onClick={onClose}
    >
      <div
        className="relative w-full max-w-2xl overflow-hidden bg-white rounded-lg shadow-xl dark:bg-gray-800"
        onClick={(e) => e.stopPropagation()}
      >
        <div className="p-4 border-b dark:border-gray-600">
             <h3 className="text-xl font-semibold text-gray-900 dark:text-white">
                {video.title}
             </h3>
        </div>
        <div className="p-4 text-center bg-gray-900">
            <img src={video.thumbnailUrl} alt={video.title} className="object-contain w-full mx-auto max-h-80" />
            <p className="mt-2 text-sm text-gray-400">در حال پخش... (شبیه‌سازی)</p>
        </div>
        <div className="w-full bg-gray-200 dark:bg-gray-700 h-2.5">
            <div 
                className="bg-indigo-600 h-2.5" 
                style={{ width: `${progress}%`, transition: 'width 0.05s linear' }}
            ></div>
        </div>
      </div>
    </div>
  );
};

export default VideoPlayerModal;
