USE gki_menteng;

SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE sunday_services;
TRUNCATE TABLE sunday_schedules;
TRUNCATE TABLE volunteers;
TRUNCATE TABLE ministries;
TRUNCATE TABLE calendar_events;
TRUNCATE TABLE daily_reflections;
TRUNCATE TABLE news;
TRUNCATE TABLE church_activities;
TRUNCATE TABLE pastoral_team;
TRUNCATE TABLE church_profile;
TRUNCATE TABLE site_stats;

SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO news (title, content, date, image, category) VALUES
('Ibadah Natal 2024', 'Persiapkan diri menyambut Natal dengan penuh sukacita. Ibadah Natal akan diadakan pada 25 Desember 2024.', '2024-12-15', 'https://picsum.photos/seed/church1/800/400', 'Ibadah'),
('Retreat Pemuda 2025', 'Pendaftaran retreat pemuda sudah dibuka! Bergabunglah dalam retreat yang akan diadakan di Puncak.', '2024-12-10', 'https://picsum.photos/seed/church2/800/400', 'Kegiatan'),
('Pelayanan Sosial', 'Mari berpartisipasi dalam kegiatan sosial membantu masyarakat sekitar.', '2024-12-08', 'https://picsum.photos/seed/church3/800/400', 'Sosial');

INSERT INTO daily_reflections (reflection_date, verse, content, author, theme) VALUES
('2024-12-16', 'Mazmur 23:1', 'Tuhan adalah gembalaku, takkan kekurangan aku. Ia membaringkan aku di padang yang berumput hijau, Ia membimbing aku ke air yang tenang.', 'Pdt. Yohanes Saragih', 'Pemeliharaan Tuhan');

INSERT INTO site_stats (id, members, ministries, volunteers, events) VALUES
(1, 1250, 15, 85, 24);

INSERT INTO calendar_events (title, event_date, time, end_time, location, description, category, color) VALUES
('Ibadah Minggu Pagi I', '2024-12-17', '07:00', '09:00', 'Gedung Gereja Utama', 'Ibadah Umum dengan Liturgi Lengkap', 'Ibadah', '#1a237e'),
('Ibadah Minggu Pagi II', '2024-12-17', '10:00', '12:00', 'Gedung Gereja Utama', 'Ibadah Umum dengan Liturgi Lengkap', 'Ibadah', '#1a237e'),
('Sekolah Minggu', '2024-12-17', '09:00', '10:30', 'Ruang Sekolah Minggu', 'Kelas Sekolah Minggu untuk anak-anak', 'Pendidikan', '#c5a572'),
('Persekutuan Pemuda', '2024-12-18', '18:00', '20:00', 'Aula Gereja', 'Persekutuan rutin pemuda GKI Menteng', 'Persekutuan', '#d4af37'),
('Latihan Paduan Suara', '2024-12-19', '16:00', '18:00', 'Ruang Musik', 'Latihan rutin paduan suara', 'Musik', '#283593'),
('Doa Malam', '2024-12-19', '19:00', '21:00', 'Ruang Doa', 'Persekutuan doa malam', 'Doa', '#1a237e'),
('Ibadah Natal', '2024-12-25', '09:00', '11:00', 'Gedung Gereja Utama', 'Ibadah Perayaan Natal', 'Ibadah', '#c5a572');

INSERT INTO ministries (name, volunteer_count) VALUES
('Kebaktian Umum', 15),
('Tim Musik', 20),
('Sekolah Minggu', 12),
('Tim Multimedia', 8),
('Tim Hospitality', 10),
('Konseling', 5);

INSERT INTO volunteers (name, role, ministry, schedule, status, phone, email, experience, image) VALUES
('Budi Santoso', 'Pengkhotbah', 'Kebaktian Umum', 'Minggu, 17 Des 2024 - 07:00', 'Scheduled', '08123456789', 'budi.santoso@example.com', '5 tahun', 'https://i.pravatar.cc/150?img=1'),
('Sarah Wijaya', 'Pemimpin Pujian', 'Tim Musik', 'Minggu, 17 Des 2024 - 07:00', 'Scheduled', '08123456780', 'sarah.wijaya@example.com', '3 tahun', 'https://i.pravatar.cc/150?img=5'),
('David Christian', 'Pemain Musik', 'Tim Musik', 'Minggu, 17 Des 2024 - 07:00', 'Scheduled', '08123456781', 'david.christian@example.com', '2 tahun', 'https://i.pravatar.cc/150?img=3'),
('Maria Kusuma', 'Penerima Tamu', 'Tim Hospitality', 'Minggu, 17 Des 2024 - 06:30', 'Scheduled', '08123456782', 'maria.kusuma@example.com', '4 tahun', 'https://i.pravatar.cc/150?img=9'),
('Yohanes Peter', 'Operator Multimedia', 'Tim Multimedia', 'Minggu, 17 Des 2024 - 06:30', 'Available', '08123456783', 'yohanes.peter@example.com', '2 tahun', 'https://i.pravatar.cc/150?img=8'),
('Ruth Elisabeth', 'Guru Sekolah Minggu', 'Sekolah Minggu', 'Minggu, 17 Des 2024 - 09:00', 'Scheduled', '08123456784', 'ruth.elisabeth@example.com', '6 tahun', 'https://i.pravatar.cc/150?img=10');

INSERT INTO sunday_schedules (schedule_date) VALUES ('2024-12-17');

INSERT INTO sunday_services (schedule_id, time_slot, preacher, worship_leader, musicians, hospitality, multimedia, sunday_school) VALUES
(1, '07:00 - 09:00', 'Pdt. Yohanes Saragih', 'Sarah Wijaya', '["David Christian","Andre Wibowo"]', '["Maria Kusuma","John Doe"]', 'Available', '["Ruth Elisabeth","Debora Sari"]'),
(1, '10:00 - 12:00', 'Pdt. Andreas Gunawan', 'TBD', '["TBD"]', '["TBD"]', 'Available', '["TBD"]');

INSERT INTO church_profile (name, full_name, established, address, phone, email, website, denomination, vision, mission, history, description) VALUES
('GKI Menteng', 'Gereja Kristen Indonesia Menteng', '1948', 'Jl. Teuku Umar No. 2, Menteng, Jakarta Pusat 10350', '(021) 3192-3456', 'info@gkimenteng.org', 'www.gkimenteng.org', 'Gereja Kristen Indonesia (GKI)', 'Menjadi gereja yang memberkati dan menjadi berkat bagi masyarakat', '["Memberitakan Injil Yesus Kristus kepada semua orang","Membangun persekutuan yang saling mengasihi","Melayani masyarakat melalui berbagai program sosial","Mendidik jemaat dalam iman Kristen yang dewasa"]', 'GKI Menteng didirikan pada tahun 1948 sebagai bagian dari sinode Gereja Kristen Indonesia. Sejak awal berdirinya, gereja ini telah menjadi tempat ibadah dan pusat kegiatan rohani bagi masyarakat di kawasan Menteng dan sekitarnya.', 'GKI Menteng adalah gereja yang inklusif dan terbuka bagi semua orang. Kami berkomitmen untuk menjadi komunitas iman yang bertumbuh dalam Kristus dan melayani sesama dengan kasih.');

INSERT INTO pastoral_team (name, position, education, email, image, sort_order) VALUES
('Pdt. Yohanes Saragih', 'Pendeta Kepala', 'S.Th., M.Div.', 'yohanes.saragih@gkimenteng.org', 'https://i.pravatar.cc/300?img=60', 1),
('Pdt. Andreas Gunawan', 'Pendeta', 'S.Th., M.Th.', 'andreas.gunawan@gkimenteng.org', 'https://i.pravatar.cc/300?img=61', 2),
('Pdt. Maria Lestari', 'Pendeta', 'S.Th., M.Div.', 'maria.lestari@gkimenteng.org', 'https://i.pravatar.cc/300?img=62', 3);

INSERT INTO church_activities (name, time, location, sort_order) VALUES
('Ibadah Minggu Pagi I', 'Minggu, 07:00 - 09:00', 'Gedung Utama', 1),
('Ibadah Minggu Pagi II', 'Minggu, 10:00 - 12:00', 'Gedung Utama', 2),
('Sekolah Minggu', 'Minggu, 09:00 - 10:30', 'Ruang Sekolah Minggu', 3),
('Persekutuan Doa', 'Rabu, 19:00 - 21:00', 'Ruang Doa', 4),
('Persekutuan Pemuda', 'Jumat, 18:00 - 20:00', 'Aula Gereja', 5),
('Latihan Paduan Suara', 'Sabtu, 16:00 - 18:00', 'Ruang Musik', 6);
